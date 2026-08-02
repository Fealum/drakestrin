<?php

namespace App\Services\Board;

use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\Board\ThreadSubscription;
use App\Models\User;
use App\Support\ThreadEmailFrequency;

class ThreadSubscriptionService
{
    public function __construct(
        private ThreadReadService $reads,
        private ThreadNotificationMailer $mailer,
    ) {}

    public function subscribe(User $user, Thread $thread, ?ThreadEmailFrequency $frequency = null): ThreadSubscription
    {
        $subscription = ThreadSubscription::firstOrNew([
            'user_id' => $user->id,
            'thread_id' => $thread->id,
        ]);

        if (! $subscription->exists) {
            $subscription->email_frequency = $frequency ?? $this->reads->preference($user)->default_email_frequency;
            $subscription->last_emailed_post_id = $thread->last_post_id ?: null;
            $subscription->save();
        }

        return $subscription;
    }

    public function afterPostCreated(Post $post): void
    {
        $post->loadMissing(['author', 'thread.board', 'character']);
        $user = $post->author;

        if (! $user || ! $post->thread) {
            return;
        }

        $this->reads->markPost($user, $post->thread, $post->id);

        if ($this->reads->preference($user)->auto_subscribe) {
            $this->subscribe($user, $post->thread);
        }

        $this->mailer->processPost($post);
    }
}
