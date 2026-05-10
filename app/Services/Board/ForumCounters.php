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
            ->where('thread', $thread->id)
            ->orderBy('time')
            ->orderBy('id')
            ->first();

        $lastPost = Post::query()
            ->where('thread', $thread->id)
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first();

        $thread->update([
            'post__total' => Post::where('thread', $thread->id)->count(),
            'post__first' => $firstPost?->id ?? 0,
            'post__first_time' => $firstPost?->time?->timestamp ?? 0,
            'post__last' => $lastPost?->id ?? 0,
            'post__last_time' => $lastPost?->time?->timestamp ?? 0,
        ]);
    }

    public function refreshBoard(?Board $board): void
    {
        if (! $board) {
            return;
        }

        $lastPost = Post::query()
            ->where('board', $board->id)
            ->orderByDesc('time')
            ->orderByDesc('id')
            ->first();

        $board->update([
            'thread__total' => Thread::where('board', $board->id)->count(),
            'post__total' => Post::where('board', $board->id)->count(),
            'post__last' => $lastPost?->id ?? 0,
            'post__last_time' => $lastPost?->time?->timestamp ?? 0,
        ]);
    }

    public function refreshUser(int $userId): void
    {
        DB::table('dra_user')
            ->where('id', $userId)
            ->update(['post__total' => Post::where('user', $userId)->count()]);
    }

    public function refreshCharacter(int $characterId): void
    {
        DB::table('dra_character')
            ->where('id', $characterId)
            ->update(['post__total' => Post::where('character', $characterId)->count()]);
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
