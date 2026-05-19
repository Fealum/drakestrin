<?php

namespace App\Services\Board;

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread;
use Illuminate\Support\Facades\DB;

class ForumCounters
{
    public function refreshThread(Thread $thread): void
    {
        $firstPost = Post::query()
            ->where('thread_id', $thread->id)
            ->orderBy('time')
            ->orderBy('id')
            ->first();

        $lastPost = Post::query()
            ->where('thread_id', $thread->id)
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first();

        $thread->update([
            'post_count' => Post::where('thread_id', $thread->id)->count(),
            'first_post_id' => $firstPost?->id ?? 0,
            'first_post_at' => $firstPost?->time?->timestamp ?? 0,
            'last_post_id' => $lastPost?->id ?? 0,
            'last_post_at' => $lastPost?->time?->timestamp ?? 0,
        ]);
    }

    public function refreshBoard(?Board $board): void
    {
        if (! $board) {
            return;
        }

        $lastPost = Post::query()
            ->where('board_id', $board->id)
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first();

        $board->update([
            'thread_count' => Thread::where('board_id', $board->id)->count(),
            'post_count' => Post::where('board_id', $board->id)->count(),
            'last_post_id' => $lastPost?->id ?? 0,
            'last_post_at' => $lastPost?->time?->timestamp ?? 0,
        ]);
    }

    public function refreshUser(int $userId): void
    {
        DB::table('users')
            ->where('id', $userId)
            ->update(['post_count' => Post::where('user_id', $userId)->count()]);
    }

    public function refreshCharacter(int $characterId): void
    {
        DB::table('characters')
            ->where('id', $characterId)
            ->update(['post_count' => Post::where('character_id', $characterId)->count()]);
    }

    public function refreshUsers(iterable $userIds): void
    {
        foreach (collect($userIds)->filter()->unique() as $userId) {
            $this->refreshUser((int) $userId);
        }
    }

    public function refreshCharacters(iterable $characterIds): void
    {
        foreach (collect($characterIds)->filter()->unique() as $characterId) {
            $this->refreshCharacter((int) $characterId);
        }
    }
}
