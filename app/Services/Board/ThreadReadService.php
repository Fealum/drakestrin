<?php

namespace App\Services\Board;

use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\Board\ThreadRead;
use App\Models\User;
use App\Models\User\UserPreference;
use App\Support\ThreadEmailFrequency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ThreadReadService
{
    public function preference(User $user): UserPreference
    {
        $baseline = $user->lastvisit?->timestamp ?? now()->timestamp;

        return UserPreference::firstOrCreate(['user_id' => $user->id], [
            'auto_subscribe' => true,
            'default_email_frequency' => $user->receiveemails
                ? ThreadEmailFrequency::ONCE_UNTIL_READ
                : ThreadEmailFrequency::NONE,
            'read_tracking_started_at' => $baseline,
            'read_tracking_started_post_id' => Post::query()->where('time', '<=', $baseline)->max('id') ?: 0,
            'last_daily_digest_at' => now()->timestamp,
            'last_weekly_digest_at' => now()->timestamp,
        ]);
    }

    public function unreadThreadIds(Collection $threads, User $user): Collection
    {
        $baseline = (int) $this->preference($user)->read_tracking_started_post_id;
        $reads = ThreadRead::query()
            ->where('user_id', $user->id)
            ->whereIn('thread_id', $threads->pluck('id'))
            ->pluck('last_read_post_id', 'thread_id');

        return $threads->filter(function (Thread $thread) use ($baseline, $reads) {
            if ((int) $thread->last_post_id <= $baseline) {
                return false;
            }

            return (int) $thread->last_post_id > (int) ($reads[$thread->id] ?? 0);
        })->pluck('id');
    }

    public function isUnread(Thread $thread, User $user): bool
    {
        return $this->unreadThreadIds(collect([$thread]), $user)->contains($thread->id);
    }

    public function firstUnreadPost(Thread $thread, User $user): ?Post
    {
        $baseline = (int) $this->preference($user)->read_tracking_started_post_id;
        $cursor = (int) ThreadRead::query()
            ->where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->value('last_read_post_id');

        return $thread->posts()
            ->where('id', '>', max($baseline, $cursor))
            ->first();
    }

    public function hasReadThrough(User $user, Thread $thread, ?int $postId): bool
    {
        if (! $postId) {
            return true;
        }

        $baseline = (int) $this->preference($user)->read_tracking_started_post_id;

        if ($postId <= $baseline) {
            return true;
        }

        return (int) ThreadRead::query()
            ->where('user_id', $user->id)
            ->where('thread_id', $thread->id)
            ->value('last_read_post_id') >= $postId;
    }

    public function markDisplayed(User $user, Thread $thread, Collection $posts): void
    {
        $baseline = (int) $this->preference($user)->read_tracking_started_post_id;
        $postId = $posts
            ->filter(fn (Post $post) => $post->id > $baseline)
            ->max('id');

        if ($postId) {
            $this->markPost($user, $thread, (int) $postId);
        }
    }

    public function markPost(User $user, Thread $thread, int $postId): void
    {
        DB::table('thread_reads')->upsert([[
            'user_id' => $user->id,
            'thread_id' => $thread->id,
            'last_read_post_id' => $postId,
            'read_at' => now()->timestamp,
        ]], ['user_id', 'thread_id'], [
            'last_read_post_id' => DB::raw('GREATEST(last_read_post_id, VALUES(last_read_post_id))'),
            'read_at' => DB::raw('GREATEST(read_at, VALUES(read_at))'),
        ]);
    }

    public function markAll(User $user): void
    {
        $preference = $this->preference($user);
        $preference->read_tracking_started_at = now()->timestamp;
        $preference->read_tracking_started_post_id = Post::query()->max('id') ?: 0;
        $preference->save();
        ThreadRead::query()->where('user_id', $user->id)->delete();
    }
}
