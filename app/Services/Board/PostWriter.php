<?php

namespace App\Services\Board;

use App\Data\Board\CreatePostData;
use App\Data\Economy\InventoryOwner;
use App\Data\Economy\TransferParticipant;
use App\Data\Board\UpdatePostData;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\User\Character;
use App\Models\User;
use App\Services\Economy\TransferService;
use App\Services\PermissionService;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class PostWriter
{
    public function __construct(
        private ForumCounters $counters,
        private PermissionService $permissions,
        private TransferService $transfers,
    ) {
    }

    public function create(ForumThread $thread, User $user, CreatePostData $data, string $ip): Post
    {
        return DB::transaction(function () use ($thread, $user, $data, $ip) {
            $time = now()->timestamp;
            $character = $this->resolveCharacterForCreate($thread, $user, $data, $time);

            $post = Post::create([
                'board_id' => $thread->board_id,
                'thread_id' => $thread->id,
                'user_id' => $user->id,
                'character_id' => $character->id,
                'time' => $time,
                'message' => $data->message,
                'smilies' => (int) $data->smilies,
                'signature' => (int) $data->signature,
                'ip' => $ip,
            ]);

            if ($data->hasTransfer()) {
                $this->attachTransfer($thread, $user, $character, $post, $data);
            }

            $this->counters->refreshThread($thread);
            $this->counters->refreshBoard($thread->board);
            $this->counters->refreshUser($user->id);
            $this->counters->refreshCharacter($character->id);

            return $post;
        });
    }

    public function update(Post $post, User $user, UpdatePostData $data): void
    {
        $character = $this->userCharacter($user, $data->characterId);
        $oldCharacterId = $post->character_id;

        DB::transaction(function () use ($post, $character, $data, $oldCharacterId) {
            $post->update([
                'character_id' => $character->id,
                'message' => $data->message,
            ]);

            if ($oldCharacterId !== $character->id) {
                $this->counters->refreshCharacter($oldCharacterId);
                $this->counters->refreshCharacter($character->id);

                if ($character->regdate && $character->regdate->timestamp > $post->time->timestamp) {
                    $character->update(['regdate' => $post->time->timestamp]);
                }
            }
        });
    }

    public function delete(Post $post): bool
    {
        $thread = $post->thread;
        $board = $post->board;
        $userId = $post->user_id;
        $characterId = $post->character_id;
        $deletesThread = $thread->posts()->count() === 1;

        DB::transaction(function () use ($post, $thread, $board, $userId, $characterId, $deletesThread) {
            $post->delete();

            if ($deletesThread) {
                $thread->delete();
            } else {
                $this->counters->refreshThread($thread);
            }

            $this->counters->refreshBoard($board);
            $this->counters->refreshUser($userId);
            $this->counters->refreshCharacter($characterId);
        });

        return $deletesThread;
    }

    private function resolveCharacterForCreate(ForumThread $thread, User $user, CreatePostData $data, int $time): Character
    {
        if ($data->character !== 'new') {
            if (! ctype_digit($data->character)) {
                throw ValidationException::withMessages([
                    'character' => 'Bitte wähle einen Charakter aus.',
                ]);
            }

            return $this->userCharacter($user, (int) $data->character);
        }

        abort_unless($this->permissions->allows('createcharacter', $thread, $user), 403);

        $name = $data->newCharacterName ?? '';

        if ($name === '') {
            throw ValidationException::withMessages([
                'newcharname' => 'Bitte gib einen Namen für den neuen Charakter ein.',
            ]);
        }

        return Character::create([
            'name' => $name,
            'regdate' => $time,
            'user_id' => $user->id,
            'usertext' => '',
        ]);
    }

    private function attachTransfer(ForumThread $thread, User $user, Character $sender, Post $post, CreatePostData $data): void
    {
        abort_unless($this->permissions->allows('transfer', $thread, $user), 403);
        abort_unless($thread->currentScene()->exists(), 403);

        if ($data->transferRecipientId === null) {
            throw ValidationException::withMessages([
                'recipient' => 'Bitte wähle einen Empfänger aus.',
            ]);
        }

        if ($data->transferRecipientId === $sender->id) {
            throw ValidationException::withMessages([
                'recipient' => 'Sender und Empfänger müssen verschieden sein.',
            ]);
        }

        try {
            $this->transfers->transferInventories(
                postId: $post->id,
                sender: TransferParticipant::character($sender->id),
                recipient: TransferParticipant::character($data->transferRecipientId),
                source: new InventoryOwner(PermissionEntityType::CHARACTER, $sender->id),
                target: new InventoryOwner(PermissionEntityType::CHARACTER, $data->transferRecipientId),
                items: $data->transferItems,
            );
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'inventory' => 'Keine übertragbaren Gegenstände ausgewählt.',
            ]);
        }
    }

    private function userCharacter(User $user, int $characterId): Character
    {
        return $user->characters()
            ->whereKey($characterId)
            ->firstOrFail();
    }
}
