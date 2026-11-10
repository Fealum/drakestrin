<?php

namespace App\Repositories\Board;

use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\User;
use App\Models\User\Character;
use Illuminate\Support\Collection;

class ThreadParticipantRepository
{
    /** @return Collection<int, Character> */
    public function postingCharacters(User $user, Thread $thread): Collection
    {
        return $user->characters()
            ->reorder()
            ->withMax([
                'posts as thread_last_post_at' => fn ($query) => $query->where('thread_id', $thread->id),
            ], 'time')
            ->withMax([
                'posts as thread_last_post_id' => fn ($query) => $query->where('thread_id', $thread->id),
            ], 'id')
            ->withMax('posts as last_post_at', 'time')
            ->withMax('posts as last_post_id', 'id')
            ->orderByRaw('CASE WHEN thread_last_post_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('thread_last_post_at')
            ->orderByDesc('thread_last_post_id')
            ->orderByRaw('CASE WHEN last_post_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('last_post_at')
            ->orderByDesc('last_post_id')
            ->orderByRaw('LOWER(name)')
            ->get();
    }

    /** @return Collection<int, Character> */
    public function eligibleRecipients(Thread $thread, ?int $includeCharacterId = null): Collection
    {
        $activity = Post::query()
            ->where('thread_id', $thread->id)
            ->whereNotNull('character_id')
            ->selectRaw('character_id, MAX(time) AS last_post_at')
            ->groupBy('character_id');

        $latest = (clone $activity)->orderByDesc('last_post_at')->limit(10)->pluck('last_post_at', 'character_id');
        $recent = (clone $activity)->havingRaw('MAX(time) >= ?', [now()->subMonths(6)->timestamp])
            ->pluck('last_post_at', 'character_id');
        $lastSeen = $latest->union($recent);
        if ($includeCharacterId) {
            $lastSeen->put($includeCharacterId, $lastSeen->get($includeCharacterId, PHP_INT_MAX));
        }

        return Character::query()->with('user')->whereKey($lastSeen->keys())->get()
            ->sortByDesc(fn (Character $character) => $lastSeen->get($character->id, 0))
            ->values();
    }

    public function isEligibleRecipient(Thread $thread, int $characterId, ?int $includeCharacterId = null): bool
    {
        return $this->eligibleRecipients($thread, $includeCharacterId)->contains('id', $characterId);
    }
}
