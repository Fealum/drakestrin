<?php

namespace Tests\Feature;

use App\Mail\ThreadDigestMail;
use App\Mail\ThreadReplyMail;
use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\Board\ThreadSubscription;
use App\Models\User;
use App\Models\User\Character;
use App\Models\User\UserPreference;
use App\Services\Board\ThreadNotificationMailer;
use App\Services\Board\ThreadReadService;
use App\Services\Board\ThreadSubscriptionService;
use App\Support\PermissionEntityType;
use App\Support\ThreadEmailFrequency;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ThreadSubscriptionTest extends TestCase
{
    use DatabaseTransactions;

    private User $author;

    private User $subscriber;

    private Character $character;

    private Board $board;

    private Thread $thread;

    private Post $initialPost;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Mail::fake();

        $this->author = User::factory()->create();
        $this->subscriber = User::factory()->create();
        $this->character = Character::factory()->create(['user_id' => $this->author->id]);
        $this->board = Board::factory()->create();
        $this->thread = Thread::factory()->create(['board_id' => $this->board->id]);
        $this->initialPost = $this->makePost($this->author, $this->character, now()->subMinute()->timestamp, 'Anfang');
        $this->thread->update([
            'first_post_id' => $this->initialPost->id,
            'last_post_id' => $this->initialPost->id,
            'post_count' => 1,
            'first_post_at' => $this->initialPost->time,
            'last_post_at' => $this->initialPost->time,
        ]);

        foreach ([$this->author, $this->subscriber] as $user) {
            UserPreference::create([
                'user_id' => $user->id,
                'auto_subscribe' => $user->is($this->author),
                'default_email_frequency' => ThreadEmailFrequency::ONCE_UNTIL_READ,
                'read_tracking_started_at' => now()->subHour()->timestamp,
                'read_tracking_started_post_id' => $this->initialPost->id,
                'last_daily_digest_at' => now()->timestamp,
                'last_weekly_digest_at' => now()->timestamp,
            ]);
        }
    }

    public function test_read_cursor_advances_only_through_displayed_posts(): void
    {
        $posts = collect();
        for ($i = 1; $i <= 21; $i++) {
            $posts->push($this->makePost($this->author, $this->character, now()->addSeconds($i)->timestamp, 'Beitrag '.$i));
        }
        $last = $posts->last();
        $this->thread->update(['last_post_id' => $last->id, 'last_post_at' => $last->time, 'post_count' => 22]);
        $reads = app(ThreadReadService::class);

        $reads->markDisplayed($this->subscriber, $this->thread, $posts->take(20));

        $this->assertTrue($reads->isUnread($this->thread->fresh(), $this->subscriber));
        $this->assertSame($last->id, $reads->firstUnreadPost($this->thread, $this->subscriber)?->id);

        $reads->markDisplayed($this->subscriber, $this->thread, collect([$last]));
        $this->assertFalse($reads->isUnread($this->thread->fresh(), $this->subscriber));
    }

    public function test_post_creation_auto_subscribes_author_and_sends_immediate_mail_to_other_subscriber(): void
    {
        ThreadSubscription::create([
            'user_id' => $this->subscriber->id,
            'thread_id' => $this->thread->id,
            'email_frequency' => ThreadEmailFrequency::IMMEDIATE,
            'last_emailed_post_id' => $this->initialPost->id,
        ]);
        $post = $this->makePost($this->author, $this->character, now()->timestamp, 'Neuigkeiten');
        $this->thread->update(['last_post_id' => $post->id, 'last_post_at' => $post->time, 'post_count' => 2]);

        app(ThreadSubscriptionService::class)->afterPostCreated($post);

        Mail::assertSent(ThreadReplyMail::class, fn ($mail) => $mail->hasTo($this->subscriber->email));
        $this->assertDatabaseHas('thread_subscriptions', [
            'user_id' => $this->author->id,
            'thread_id' => $this->thread->id,
            'email_frequency' => ThreadEmailFrequency::ONCE_UNTIL_READ->value,
        ]);
    }

    public function test_once_until_read_suppresses_mail_until_emailed_post_is_read(): void
    {
        $subscription = ThreadSubscription::create([
            'user_id' => $this->subscriber->id,
            'thread_id' => $this->thread->id,
            'email_frequency' => ThreadEmailFrequency::ONCE_UNTIL_READ,
            'last_emailed_post_id' => $this->initialPost->id,
        ]);
        $reads = app(ThreadReadService::class);
        $reads->markPost($this->subscriber, $this->thread, $this->initialPost->id);
        $first = $this->makePost($this->author, $this->character, now()->timestamp, 'Erste Antwort');
        $second = $this->makePost($this->author, $this->character, now()->addSecond()->timestamp, 'Zweite Antwort');
        $mailer = app(ThreadNotificationMailer::class);

        $mailer->processPost($first);
        $mailer->processPost($second);
        Mail::assertSentCount(1);

        $reads->markPost($this->subscriber, $this->thread, $first->id);
        $mailer->processPost($second);
        Mail::assertSentCount(2);
        $this->assertSame($second->id, $subscription->fresh()->last_emailed_post_id);
    }

    public function test_weekly_digest_is_sent_friday_at_eighteen_hundred(): void
    {
        CarbonImmutable::setTestNow('2026-08-07 18:01:00 Europe/Berlin');
        $preference = $this->subscriber->preference;
        $preference->update(['last_weekly_digest_at' => CarbonImmutable::parse('2026-07-31 18:00 Europe/Berlin')->timestamp]);
        ThreadSubscription::create([
            'user_id' => $this->subscriber->id,
            'thread_id' => $this->thread->id,
            'email_frequency' => ThreadEmailFrequency::WEEKLY,
            'last_emailed_post_id' => $this->initialPost->id,
        ]);
        $post = $this->makePost($this->author, $this->character, now()->subMinute()->timestamp, 'Wochenbeitrag');
        $this->thread->update(['last_post_id' => $post->id, 'last_post_at' => $post->time, 'post_count' => 2]);

        app(ThreadNotificationMailer::class)->processPending();

        Mail::assertSent(ThreadDigestMail::class, fn ($mail) => $mail->hasTo($this->subscriber->email) && $mail->period === 'weekly');
        CarbonImmutable::setTestNow();
    }

    public function test_subscription_management_and_personal_filters_work_without_javascript(): void
    {
        ThreadSubscription::create([
            'user_id' => $this->subscriber->id,
            'thread_id' => $this->thread->id,
            'email_frequency' => ThreadEmailFrequency::NONE,
            'last_emailed_post_id' => $this->initialPost->id,
        ]);

        $this->actingAs($this->subscriber)
            ->get(route('subscriptions.index'))
            ->assertOk()
            ->assertSee($this->thread->name);
        $this->get(route('board.filter', ['filter' => 'scope:subscribed']))
            ->assertOk()
            ->assertSee($this->thread->name);
        $this->actingAs($this->author)
            ->get(route('board.filter', ['filter' => 'scope:participated']))
            ->assertOk()
            ->assertSee($this->thread->name);
    }

    public function test_default_email_frequency_follows_the_user_email_preference(): void
    {
        $this->subscriber->preference()->delete();
        $this->subscriber->update(['receiveemails' => false]);

        $preference = app(ThreadReadService::class)->preference($this->subscriber->fresh());

        $this->assertSame(ThreadEmailFrequency::NONE, $preference->default_email_frequency);

        $this->actingAs($this->subscriber)
            ->post(route('forum.settings'), [
                'auto_subscribe' => true,
                'default_email_frequency' => ThreadEmailFrequency::DAILY->value,
            ])
            ->assertRedirect();

        $this->assertTrue($this->subscriber->fresh()->receiveemails);
        $this->assertSame(
            ThreadEmailFrequency::DAILY,
            $preference->fresh()->default_email_frequency,
        );
    }

    public function test_subscriber_identity_requires_the_dedicated_permission(): void
    {
        ThreadSubscription::create([
            'user_id' => $this->subscriber->id,
            'thread_id' => $this->thread->id,
            'email_frequency' => ThreadEmailFrequency::NONE,
            'last_emailed_post_id' => $this->initialPost->id,
        ]);

        $outsider = User::factory()->create();
        $this->actingAs($outsider)
            ->get(route('thread.subscribers', ['thread' => $this->thread->id]))
            ->assertForbidden();

        DB::table('permissions')->insert([
            'recipient_type' => PermissionEntityType::USER->value,
            'recipient_id' => $this->author->id,
            'subject_type' => 0,
            'subject_id' => 0,
            'permit_id' => DB::table('permits')->where('name', 'viewthreadsubscriptions')->value('id'),
            'value' => 2,
        ]);
        Cache::flush();

        $this->actingAs($this->author)
            ->get(route('thread.subscribers', ['thread' => $this->thread->id]))
            ->assertOk()
            ->assertSee($this->subscriber->name)
            ->assertDontSee($this->subscriber->email);
    }

    private function makePost(User $user, Character $character, int $time, string $message): Post
    {
        return Post::factory()->create([
            'board_id' => $this->board->id,
            'thread_id' => $this->thread->id,
            'user_id' => $user->id,
            'character_id' => $character->id,
            'time' => $time,
            'message' => $message,
        ]);
    }
}
