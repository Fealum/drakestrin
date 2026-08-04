<?php

namespace App\Services\Board;

use App\Mail\ThreadDigestMail;
use App\Mail\ThreadReplyMail;
use App\Models\Board\Post;
use App\Models\Board\ThreadSubscription;
use App\Models\User\UserPreference;
use App\Support\ThreadEmailFrequency;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ThreadNotificationMailer
{
    public function __construct(private ThreadReadService $reads) {}

    public function processPost(Post $post): void
    {
        $post->loadMissing(['author', 'character', 'thread.board', 'elements.message']);

        ThreadSubscription::query()
            ->with(['user', 'thread.board'])
            ->where('thread_id', $post->thread_id)
            ->whereIn('email_frequency', [ThreadEmailFrequency::IMMEDIATE, ThreadEmailFrequency::ONCE_UNTIL_READ])
            ->get()
            ->each(fn (ThreadSubscription $subscription) => $this->sendPost($subscription, $post));
    }

    public function processPending(): void
    {
        $lock = Cache::lock('forum-subscription-mail', 55);

        if (! $lock->get()) {
            return;
        }

        try {
            ThreadSubscription::query()
                ->with(['user', 'thread.board'])
                ->whereIn('email_frequency', [ThreadEmailFrequency::IMMEDIATE, ThreadEmailFrequency::ONCE_UNTIL_READ])
                ->chunkById(100, function ($subscriptions) {
                    foreach ($subscriptions as $subscription) {
                        $posts = $subscription->thread->posts()
                            ->with(['author', 'character', 'thread.board', 'elements.message'])
                            ->where('id', '>', (int) $subscription->last_emailed_post_id)
                            ->orderBy('id')
                            ->get();

                        foreach ($posts as $post) {
                            $this->sendPost($subscription->fresh(['user', 'thread.board']), $post);
                        }
                    }
                });

            $this->processDigest(ThreadEmailFrequency::DAILY);
            $this->processDigest(ThreadEmailFrequency::WEEKLY);
        } finally {
            $lock->release();
        }
    }

    private function sendPost(ThreadSubscription $subscription, Post $post): void
    {
        $lock = Cache::lock('forum-subscription-mail:'.$subscription->id, 30);

        if (! $lock->get()) {
            return;
        }

        try {
            $subscription = $subscription->fresh(['user', 'thread.board']);

            if (! $subscription->user || ! $subscription->thread || $post->id <= (int) $subscription->last_emailed_post_id) {
                return;
            }

            if ($post->user_id === $subscription->user_id || Gate::forUser($subscription->user)->denies('view', $subscription->thread)) {
                $subscription->update(['last_emailed_post_id' => $post->id]);

                return;
            }

            if ($subscription->email_frequency === ThreadEmailFrequency::ONCE_UNTIL_READ
                && ! $this->reads->hasReadThrough($subscription->user, $subscription->thread, $subscription->last_emailed_post_id)) {
                return;
            }

            try {
                Mail::to($subscription->user->email)->send(new ThreadReplyMail($subscription, $post));
                $subscription->update(['last_emailed_post_id' => $post->id]);
            } catch (Throwable $exception) {
                Log::warning('Thread subscription email failed.', [
                    'subscription_id' => $subscription->id,
                    'post_id' => $post->id,
                    'exception' => $exception->getMessage(),
                ]);
            }
        } finally {
            $lock->release();
        }
    }

    private function processDigest(ThreadEmailFrequency $frequency): void
    {
        $column = $frequency === ThreadEmailFrequency::DAILY ? 'last_daily_digest_at' : 'last_weekly_digest_at';
        $cutoff = $this->digestCutoff($frequency);

        if (! $cutoff) {
            return;
        }

        UserPreference::query()
            ->with('user')
            ->where(fn ($query) => $query->whereNull($column)->orWhere($column, '<', $cutoff->timestamp))
            ->chunkById(100, function ($preferences) use ($column, $cutoff, $frequency) {
                foreach ($preferences as $preference) {
                    $this->sendDigest($preference, $frequency, $column, $cutoff->timestamp);
                }
            }, 'user_id');
    }

    private function sendDigest(UserPreference $preference, ThreadEmailFrequency $frequency, string $column, int $cutoff): void
    {
        $user = $preference->user;

        if (! $user) {
            return;
        }

        $subscriptions = ThreadSubscription::query()
            ->with('thread.board')
            ->where('user_id', $user->id)
            ->where('email_frequency', $frequency)
            ->get();
        $entries = collect();
        $cursors = [];

        foreach ($subscriptions as $subscription) {
            $thread = $subscription->thread;
            if (! $thread) {
                continue;
            }

            $latestExaminedId = (int) $thread->posts()
                ->where('id', '>', (int) $subscription->last_emailed_post_id)
                ->where('time', '<=', $cutoff)
                ->max('id');
            $cursors[$subscription->id] = max((int) $subscription->last_emailed_post_id, $latestExaminedId);

            if (Gate::forUser($user)->denies('view', $thread)) {
                continue;
            }

            $posts = $thread->posts()
                ->with(['character', 'author'])
                ->where('id', '>', (int) $subscription->last_emailed_post_id)
                ->where('user_id', '<>', $user->id)
                ->where('time', '<=', $cutoff)
                ->get();

            if ($posts->isNotEmpty()) {
                $entries->push([
                    'subscription' => $subscription,
                    'thread' => $thread,
                    'count' => $posts->count(),
                    'last_post' => $posts->last(),
                    'first_post' => $posts->first(),
                ]);
            }
        }

        try {
            if ($entries->isNotEmpty()) {
                Mail::to($user->email)->send(new ThreadDigestMail(
                    $user,
                    $entries,
                    $frequency === ThreadEmailFrequency::DAILY ? 'daily' : 'weekly',
                ));
            }

            foreach ($cursors as $subscriptionId => $postId) {
                ThreadSubscription::whereKey($subscriptionId)->update(['last_emailed_post_id' => $postId]);
            }
            $preference->update([$column => $cutoff]);
        } catch (Throwable $exception) {
            Log::warning('Thread subscription digest failed.', [
                'user_id' => $user->id,
                'frequency' => $frequency->value,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private function digestCutoff(ThreadEmailFrequency $frequency): ?CarbonImmutable
    {
        $now = CarbonImmutable::now('Europe/Berlin');

        if ($frequency === ThreadEmailFrequency::DAILY) {
            $cutoff = $now->setTime(18, 0);

            return $now->greaterThanOrEqualTo($cutoff) ? $cutoff : $cutoff->subDay();
        }

        $cutoff = $now->startOfWeek()->addDays(CarbonImmutable::FRIDAY - 1)->setTime(18, 0);

        return $now->greaterThanOrEqualTo($cutoff) ? $cutoff : $cutoff->subWeek();
    }
}
