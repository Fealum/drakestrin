<?php

namespace App\Services\Board;

use App\Data\Board\CreateThreadData;
use App\Data\Board\UpdateThreadData;
use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\User\Character;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ThreadWriter
{
    public function __construct(private ForumCounters $counters)
    {
    }

    public function create(Board $board, User $user, CreateThreadData $data, bool $canMarkAsImportant, string $ip): ForumThread
    {
        $character = $this->userCharacter($user, $data->characterId);

        return DB::transaction(function () use ($board, $user, $character, $data, $canMarkAsImportant, $ip) {
            $time = now()->timestamp;

            $thread = ForumThread::create([
                'board_id' => $board->id,
                'name' => $data->name,
                'first_post_at' => $time,
                'post_count' => 1,
                'last_post_at' => $time,
                'views' => 0,
                'important' => $canMarkAsImportant ? (int) $data->important : 0,
            ]);

            $post = Post::create([
                'board_id' => $board->id,
                'thread_id' => $thread->id,
                'user_id' => $user->id,
                'character_id' => $character->id,
                'time' => $time,
                'message' => $data->message,
                'smilies' => (int) $data->smilies,
                'signature' => (int) $data->signature,
                'ip' => $ip,
            ]);

            $thread->update([
                'first_post_id' => $post->id,
                'last_post_id' => $post->id,
            ]);

            $this->counters->refreshThread($thread);
            $this->counters->refreshBoard($board);
            $this->counters->refreshUser($user->id);
            $this->counters->refreshCharacter($character->id);

            return $thread;
        });
    }

    public function update(ForumThread $thread, Board $newBoard, UpdateThreadData $data, bool $canMarkAsImportant): void
    {
        $oldBoard = $thread->board;

        DB::transaction(function () use ($thread, $oldBoard, $newBoard, $data, $canMarkAsImportant) {
            $thread->update([
                'board_id' => $newBoard->id,
                'name' => $data->name,
                'important' => $canMarkAsImportant ? (int) $data->important : $thread->important,
            ]);

            if (! $oldBoard || $oldBoard->id !== $newBoard->id) {
                Post::where('thread_id', $thread->id)->update(['board_id' => $newBoard->id]);
                $this->counters->refreshBoard($oldBoard);
            }

            $this->counters->refreshThread($thread);
            $this->counters->refreshBoard($newBoard);
        });
    }

    public function delete(ForumThread $thread): void
    {
        $board = $thread->board;
        $userIds = $thread->posts()->pluck('user_id');
        $characterIds = $thread->posts()->pluck('character_id');

        DB::transaction(function () use ($thread, $board, $userIds, $characterIds) {
            Post::where('thread_id', $thread->id)->delete();
            $thread->delete();

            $this->counters->refreshBoard($board);
            $this->counters->refreshUsers($userIds);
            $this->counters->refreshCharacters($characterIds);
        });
    }

    private function userCharacter(User $user, int $characterId): Character
    {
        return $user->characters()
            ->whereKey($characterId)
            ->firstOrFail();
    }
}
