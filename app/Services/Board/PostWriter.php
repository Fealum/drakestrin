<?php

namespace App\Services\Board;

use App\Data\Board\CreatePostData;
use App\Data\Board\UpdatePostData;
use App\Data\Economy\InventoryOwner;
use App\Data\Economy\TransferContext;
use App\Data\Economy\TransferInventoryItem;
use App\Data\Economy\TransferParticipant;
use App\Exceptions\Economy\InventoryUnavailableAtStoryTime;
use App\Models\Board\Post;
use App\Models\Board\Thread as ForumThread;
use App\Models\Economy\CompanySite;
use App\Models\Economy\Inventory;
use App\Models\User;
use App\Models\User\Character;
use App\Repositories\Territory\LocationRepository;
use App\Services\Economy\TransferService;
use App\Services\PermissionService;
use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use App\Support\PostTransferAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class PostWriter
{
    public function __construct(
        private ForumCounters $counters,
        private PermissionService $permissions,
        private TransferService $transfers,
        private LocationRepository $locations,
        private ThreadSubscriptionService $subscriptions,
    ) {}

    public function create(ForumThread $thread, User $user, CreatePostData $data, string $ip): Post
    {
        $post = DB::transaction(function () use ($thread, $user, $data, $ip) {
            $thread = ForumThread::query()->whereKey($thread->id)->lockForUpdate()->firstOrFail();
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

        $this->subscriptions->afterPostCreated($post);

        return $post;
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
        if ($post->transfers()->exists()) {
            $post->update(['message' => '']);

            return false;
        }

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
        $scene = $thread->currentScene()->with('location')->first();
        abort_unless($scene?->location, 403);

        if ($scene->story_started_at === null) {
            throw ValidationException::withMessages([
                'transfer_action' => 'Für eine Handlung benötigt die aktive Szene eine Spielzeit.',
            ]);
        }

        $transferData = $data->transfer;
        abort_unless($transferData, 422);

        $characterOwner = new InventoryOwner(PermissionEntityType::CHARACTER, $sender->id);
        $locationOwner = new InventoryOwner(PermissionEntityType::LOCATION, $scene->location->id);

        if ($transferData->action === PostTransferAction::GIVE) {
            if ($transferData->recipientCharacterId === null) {
                throw ValidationException::withMessages([
                    'recipient' => 'Bitte wähle einen Empfänger aus.',
                ]);
            }

            if ($transferData->recipientCharacterId === $sender->id) {
                throw ValidationException::withMessages([
                    'recipient' => 'Sender und Empfänger müssen verschieden sein.',
                ]);
            }

            $source = $characterOwner;
            $target = new InventoryOwner(PermissionEntityType::CHARACTER, $transferData->recipientCharacterId);
            $transferSender = TransferParticipant::character($sender->id);
            $transferRecipient = TransferParticipant::character($transferData->recipientCharacterId);
        } elseif ($transferData->action === PostTransferAction::DROP) {
            $source = $characterOwner;
            $target = $locationOwner;
            $transferSender = TransferParticipant::character($sender->id);
            $transferRecipient = TransferParticipant::location($scene->location->id);
        } elseif ($transferData->action === PostTransferAction::PICKUP) {
            $source = $locationOwner;
            $target = $characterOwner;
            $transferSender = TransferParticipant::location($scene->location->id);
            $transferRecipient = TransferParticipant::character($sender->id);
        } else {
            if ($transferData->companySiteId === null) {
                throw ValidationException::withMessages(['company_site' => 'Bitte wähle einen Betriebsstandort aus.']);
            }

            $site = CompanySite::query()
                ->with('company')
                ->whereKey($transferData->companySiteId)
                ->whereIn('location_id', $this->locations->ancestorLocationIds($scene->location))
                ->firstOrFail();
            $company = $site->company;
            abort_unless($company, 404);

            if ($transferData->action === PostTransferAction::COMPANY_DEPOSIT) {
                $source = $characterOwner;
                $target = new InventoryOwner(PermissionEntityType::COMPANY_SITE, $site->id, InventoryStockState::PRODUCTION->value);
                $transferSender = TransferParticipant::character($sender->id);
                $transferRecipient = TransferParticipant::companySite($site->id);
                $inventoryItemIds = Inventory::query()
                    ->whereIn('id', collect($transferData->items)->pluck('inventoryId'))
                    ->ownedBy(PermissionEntityType::CHARACTER, $sender->id)
                    ->pluck('item_id', 'id');
                $transferDataItems = collect($transferData->items)
                    ->map(fn ($item) => new TransferInventoryItem(
                        inventoryId: $item->inventoryId,
                        requestedStack: $item->requestedStack,
                        targetWear: (int) $inventoryItemIds->get($item->inventoryId) === 1
                            ? InventoryStockState::RESERVED->value
                            : InventoryStockState::PRODUCTION->value,
                    ))
                    ->all();
            } else {
                if (! $user->can('transfer', [$site, $sender])) {
                    abort(403);
                }

                $selectedInventoryIds = collect($transferData->items)->pluck('inventoryId');
                $eligibleInventoryCount = Inventory::query()
                    ->whereIn('id', $selectedInventoryIds)
                    ->ownedBy(PermissionEntityType::COMPANY_SITE, $site->id)
                    ->where('wear', '>=', InventoryStockState::RESERVED->value)
                    ->count();

                if ($eligibleInventoryCount !== $selectedInventoryIds->count()) {
                    throw ValidationException::withMessages([
                        'inventory' => 'Ausgehändigt werden können nur Vorbehalts- und Verkaufsgüter.',
                    ]);
                }

                if ($transferData->recipientCharacterId === null) {
                    throw ValidationException::withMessages(['recipient' => 'Bitte wähle einen Empfänger aus.']);
                }

                $source = new InventoryOwner(PermissionEntityType::COMPANY_SITE, $site->id);
                $target = new InventoryOwner(PermissionEntityType::CHARACTER, $transferData->recipientCharacterId);
                $transferSender = TransferParticipant::companySite($site->id);
                $transferRecipient = TransferParticipant::character($transferData->recipientCharacterId);
            }
        }

        try {
            $this->transfers->transferInventories(
                postId: $post->id,
                sender: $transferSender,
                recipient: $transferRecipient,
                source: $source,
                target: $target,
                items: $transferDataItems ?? $transferData->items,
                context: new TransferContext(
                    threadSceneId: $scene->id,
                    storyAt: $scene->story_started_at,
                    createdByUserId: $user->id,
                    actedByCharacterId: $sender->id,
                ),
            );
        } catch (InventoryUnavailableAtStoryTime) {
            throw ValidationException::withMessages([
                'inventory' => 'Die gewählte Menge ist zu dieser Spielzeit nicht verfügbar oder wird für eine spätere Handlung benötigt.',
            ]);
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
