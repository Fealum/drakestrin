<?php

namespace App\Services\Board;

use App\Data\Board\CreateThreadData;
use App\Data\Board\PostCompositionData;
use App\Data\Board\UpdateThreadData;
use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\PostDraft;
use App\Models\Board\Thread as ForumThread;
use App\Models\Board\ThreadScene;
use App\Models\User;
use App\Models\User\Character;
use App\Support\PostElementType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class ThreadWriter
{
    public function __construct(
        private ForumCounters $counters,
        private ThreadSubscriptionService $subscriptions,
        private PostWriter $posts,
    ) {}

    public function create(Board $board, User $user, CreateThreadData $data, bool $canMarkAsImportant, string $ip): ForumThread
    {
        $character = $this->userCharacter($user, $data->characterId);

        $thread = DB::transaction(function () use ($board, $user, $character, $data, $canMarkAsImportant, $ip) {
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
                'ip' => $ip,
            ]);

            $messagePosition = $data->sceneLocationId ? 200 : 100;
            $messageElement = $post->elements()->create(['position' => $messagePosition, 'type' => PostElementType::MESSAGE]);
            $messageElement->message()->create(['message' => $data->message, 'smilies' => $data->smilies]);

            $thread->update([
                'first_post_id' => $post->id,
                'last_post_id' => $post->id,
            ]);

            if ($data->sceneLocationId) {
                $scene = ThreadScene::create([
                    'thread_id' => $thread->id,
                    'location_id' => $data->sceneLocationId,
                    'starts_at_post_id' => null,
                    'story_started_at' => $data->sceneStoryStartedAt,
                    'created_by_user_id' => $user->id,
                ]);
                $element = $post->elements()->create(['position' => 100, 'type' => PostElementType::SCENE_TRANSITION]);
                $element->sceneTransition()->create(['started_scene_id' => $scene->id]);
            }

            $this->counters->refreshThread($thread);
            $this->counters->refreshBoard($board);
            $this->counters->refreshUser($user->id);
            $this->counters->refreshCharacter($character->id);

            return $thread;
        });

        DB::afterCommit(fn () => $this->subscriptions->afterPostCreated($thread->firstPost()->firstOrFail()));

        return $thread;
    }

    public function createComposition(Board $board, User $user, PostDraft $draft, PostCompositionData $composition, string $ip): ForumThread
    {
        if (! filled($draft->title)) {
            throw ValidationException::withMessages(['title' => 'Bitte gib einen Titel ein.']);
        }

        return DB::transaction(function () use ($board, $user, $draft, $composition, $ip) {
            $time = now()->timestamp;
            $thread = ForumThread::create([
                'board_id' => $board->id,
                'name' => $draft->title,
                'first_post_at' => $time,
                'post_count' => 0,
                'last_post_at' => $time,
                'views' => 0,
                'important' => 0,
            ]);

            $post = $this->posts->createComposition($thread, $user, $draft->character_id, $composition, $ip);
            $thread->update(['first_post_id' => $post->id, 'last_post_id' => $post->id]);

            return $thread->refresh();
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
        if ($thread->posts()->whereHas('transfers')->exists()) {
            throw new InvalidArgumentException('Threads containing transfers cannot be deleted.');
        }

        $board = $thread->board;
        $userIds = $thread->posts()->pluck('user_id');
        $characterIds = $thread->posts()->pluck('character_id');

        DB::transaction(function () use ($thread, $board, $userIds, $characterIds) {
            ThreadScene::where('thread_id', $thread->id)->delete();
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
