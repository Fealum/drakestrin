<?php

namespace App\Services\Board;

use App\Data\Board\CreatePostData;
use App\Data\Board\MessageElementData;
use App\Data\Board\PollElementData;
use App\Data\Board\PostCompositionData;
use App\Data\Board\SceneTransitionElementData;
use App\Data\Board\TransferElementData;
use App\Data\Board\UpdatePostData;
use App\Data\Economy\InventoryOwner;
use App\Data\Economy\PostTransferData;
use App\Data\Economy\TransferContext;
use App\Data\Economy\TransferInventoryItem;
use App\Data\Economy\TransferParticipant;
use App\Exceptions\Economy\InventoryUnavailableAtStoryTime;
use App\Models\Board\Post;
use App\Models\Board\PostElement;
use App\Models\Board\PostSceneTransition;
use App\Models\Board\Thread as ForumThread;
use App\Models\Board\ThreadScene;
use App\Models\Economy\CompanySite;
use App\Models\Economy\Inventory;
use App\Models\User;
use App\Models\User\Character;
use App\Repositories\Board\ThreadParticipantRepository;
use App\Repositories\Territory\LocationRepository;
use App\Services\Economy\TransferService;
use App\Services\PermissionService;
use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use App\Support\PostElementType;
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
        private ThreadParticipantRepository $participants,
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
                'ip' => $ip,
            ]);

            $position = 100;
            if ($data->message !== '' || ! $data->hasTransfer()) {
                $this->createMessageElement($post, $position, new MessageElementData($data->message, $data->smilies));
                $position += 100;
            }

            if ($data->hasTransfer()) {
                $element = $this->createElement($post, $position, PostElementType::TRANSFER);
                $this->attachTransfer($thread, $user, $character, $post, $data->transfer, $thread->currentScene()->with('location')->first(), $element);
            }

            $this->counters->refreshThread($thread);
            $this->counters->refreshBoard($thread->board);
            $this->counters->refreshUser($user->id);
            $this->counters->refreshCharacter($character->id);

            return $post;
        });

        DB::afterCommit(fn () => $this->subscriptions->afterPostCreated($post));

        return $post;
    }

    public function createComposition(
        ForumThread $thread,
        User $user,
        int|string|null $characterSelection,
        PostCompositionData $composition,
        string $ip,
        ?string $newCharacterName = null,
    ): Post {
        $post = DB::transaction(function () use ($thread, $user, $characterSelection, $newCharacterName, $composition, $ip) {
            $thread = ForumThread::query()->whereKey($thread->id)->lockForUpdate()->firstOrFail();
            $character = $this->resolveCompositionCharacter($thread, $user, $characterSelection, $newCharacterName);
            $activeScene = ThreadScene::query()->with('location')->where('thread_id', $thread->id)->whereNull('ended_at')->latest('id')->first();
            $activeSceneKey = $activeScene ? 'scene:'.$activeScene->id : null;
            $messages = collect($composition->elements)->filter(fn ($element) => $element instanceof MessageElementData);

            if ($composition->elements === [] || ($messages->every(fn (MessageElementData $message) => $message->message === '')
                && collect($composition->elements)->every(fn ($element) => $element instanceof MessageElementData))) {
                throw ValidationException::withMessages(['elements' => 'Der Beitrag benötigt mindestens einen Inhalt.']);
            }

            $post = Post::create([
                'board_id' => $thread->board_id,
                'thread_id' => $thread->id,
                'user_id' => $user->id,
                'character_id' => $character->id,
                'time' => now()->timestamp,
                'ip' => $ip,
            ]);

            foreach (array_values($composition->elements) as $index => $data) {
                $position = ($index + 1) * 100;

                if ($data instanceof MessageElementData) {
                    $this->createMessageElement($post, $position, $data);

                    continue;
                }

                if ($data instanceof PollElementData) {
                    abort_unless($this->permissions->allows('createpoll', $thread, $user), 403);
                    if ($activeScene) {
                        throw ValidationException::withMessages(['elements.'.$index => 'Umfragen können nur außerhalb einer Szene stehen.']);
                    }
                    $this->createPollElement($post, $position, $data, $index);

                    continue;
                }

                if ($data instanceof SceneTransitionElementData) {
                    [$activeScene, $activeSceneKey] = $this->createSceneTransitionElement(
                        $thread, $post, $position, $data, $activeScene, $activeSceneKey, $user, $index,
                    );

                    continue;
                }

                if ($data instanceof TransferElementData) {
                    if (! $activeScene || $activeScene->story_started_at === null) {
                        throw ValidationException::withMessages(['elements.'.$index => 'Übergaben benötigen an dieser Position eine Szene mit Spielzeit.']);
                    }
                    if ($data->sceneKey && $data->sceneKey !== $activeSceneKey) {
                        throw ValidationException::withMessages(['elements.'.$index => 'Die Übergabe kann ihre zugeordnete Szene nicht verlassen.']);
                    }
                    $element = $this->createElement($post, $position, PostElementType::TRANSFER);
                    $this->attachTransfer($thread, $user, $character, $post, $data->transfer, $activeScene, $element);
                }
            }

            $this->counters->refreshThread($thread);
            $this->counters->refreshBoard($thread->board);
            $this->counters->refreshUser($user->id);
            $this->counters->refreshCharacter($character->id);

            return $post;
        });

        DB::afterCommit(fn () => $this->subscriptions->afterPostCreated($post));

        return $post;
    }

    public function update(Post $post, User $user, UpdatePostData $data): void
    {
        $character = $this->userCharacter($user, $data->characterId);
        $oldCharacterId = $post->character_id;

        DB::transaction(function () use ($post, $character, $data, $oldCharacterId) {
            if ($post->hasCharacterBoundAction() && $post->character_id !== $character->id) {
                throw ValidationException::withMessages(['character' => 'Der Charakter einer veröffentlichten Übergabe kann nicht geändert werden.']);
            }

            $messages = $post->messages()->orderBy('post_elements.position')->get();
            $messageData = $data->messages ?: [['message' => $data->message, 'smilies' => true]];
            foreach ($messages as $index => $message) {
                $row = $messageData[$index] ?? ['message' => '', 'smilies' => $message->smilies];
                $message->update(['message' => trim((string) ($row['message'] ?? '')), 'smilies' => (bool) ($row['smilies'] ?? false)]);
            }
            $hasText = $post->messages()->pluck('message')->contains(fn ($message) => trim((string) $message) !== '');
            if (! $hasText && ! $post->hasDurableElements()) {
                throw ValidationException::withMessages(['messages' => 'Ein gewöhnlicher Beitrag darf nicht vollständig leer sein.']);
            }
            $post->update(['character_id' => $character->id]);

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
        if ($post->hasDurableElements()) {
            $post->messages()->update(['message' => '']);

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

    private function resolveCompositionCharacter(
        ForumThread $thread,
        User $user,
        int|string|null $selection,
        ?string $newCharacterName,
    ): Character {
        if ((string) $selection !== 'new') {
            if (! is_numeric($selection)) {
                throw ValidationException::withMessages(['character' => 'Bitte wähle einen Charakter aus.']);
            }

            return $this->userCharacter($user, (int) $selection);
        }

        abort_unless($this->permissions->allows('createcharacter', $thread, $user), 403);
        if (blank($newCharacterName)) {
            throw ValidationException::withMessages(['newcharname' => 'Bitte gib einen Namen für den neuen Charakter ein.']);
        }

        return Character::create([
            'name' => trim($newCharacterName),
            'regdate' => now()->timestamp,
            'user_id' => $user->id,
            'usertext' => '',
        ]);
    }

    private function attachTransfer(ForumThread $thread, User $user, Character $sender, Post $post, PostTransferData $transferData, ?ThreadScene $scene, PostElement $element): void
    {
        abort_unless($this->permissions->allows('transfer', $thread, $user), 403);
        abort_unless($scene?->location, 403);

        if ($scene->story_started_at === null) {
            throw ValidationException::withMessages([
                'transfer_action' => 'Für eine Übergabe benötigt die aktive Szene eine Spielzeit.',
            ]);
        }

        [$sourceType, $sourceId, $recipientType, $recipientId, $generalized] = $this->resolveTransferParties(
            $transferData, $sender, $scene,
        );
        if (! $sourceId || ! $recipientId) {
            throw ValidationException::withMessages(['recipient_party' => 'Bitte wähle Quelle und Empfänger aus.']);
        }
        if ($sourceType === $recipientType && $sourceId === $recipientId) {
            throw ValidationException::withMessages(['recipient_party' => 'Quelle und Empfänger müssen verschieden sein.']);
        }
        if ($sourceType === PermissionEntityType::LOCATION
            && ($recipientType !== PermissionEntityType::CHARACTER || $recipientId !== $sender->id)) {
            throw ValidationException::withMessages(['recipient_party' => 'Vom Ort kann nur der beitragende Charakter etwas aufnehmen.']);
        }
        if ($generalized && $recipientType === PermissionEntityType::CHARACTER
            && ! $this->participants->isEligibleRecipient($thread, $recipientId, $sender->id)) {
            throw ValidationException::withMessages(['recipient_party' => 'Dieser Charakter gehört nicht zu den aktuellen Beteiligten des Themas.']);
        }

        $localLocationIds = $this->locations->ancestorLocationIds($scene->location);
        $sourceSite = $sourceType === PermissionEntityType::COMPANY_SITE
            ? CompanySite::query()->with('company')->whereKey($sourceId)->whereIn('location_id', $localLocationIds)->firstOrFail()
            : null;
        if ($sourceSite && ! $user->can('transfer', [$sourceSite, $sender])) {
            abort(403);
        }
        if ($recipientType === PermissionEntityType::COMPANY_SITE) {
            CompanySite::query()->whereKey($recipientId)->whereIn('location_id', $localLocationIds)->firstOrFail();
        }

        $source = new InventoryOwner($sourceType, $sourceId);
        $target = new InventoryOwner(
            $recipientType,
            $recipientId,
            $recipientType === PermissionEntityType::COMPANY_SITE ? InventoryStockState::PRODUCTION->value : 0,
        );
        $transferSender = new TransferParticipant($sourceType, $sourceId);
        $transferRecipient = new TransferParticipant($recipientType, $recipientId);
        $selectedInventoryIds = collect($transferData->items)->pluck('inventoryId');

        if ($sourceSite) {
            $eligibleInventoryCount = Inventory::query()
                ->whereIn('id', $selectedInventoryIds)
                ->ownedBy(PermissionEntityType::COMPANY_SITE, $sourceSite->id)
                ->where('wear', '>=', InventoryStockState::RESERVED->value)
                ->count();
            if ($eligibleInventoryCount !== $selectedInventoryIds->count()) {
                throw ValidationException::withMessages([
                    'inventory' => 'Ausgehändigt werden können nur Vorbehalts- und Verkaufsgüter.',
                ]);
            }
        }

        if ($recipientType === PermissionEntityType::COMPANY_SITE) {
            $inventoryItemIds = Inventory::query()->whereIn('id', $selectedInventoryIds)
                ->ownedBy($sourceType, $sourceId)->pluck('item_id', 'id');
            $transferDataItems = collect($transferData->items)->map(fn ($item) => new TransferInventoryItem(
                inventoryId: $item->inventoryId,
                requestedStack: $item->requestedStack,
                targetWear: (int) $inventoryItemIds->get($item->inventoryId) === 1
                    ? InventoryStockState::RESERVED->value
                    : InventoryStockState::PRODUCTION->value,
            ))->all();
        }

        try {
            $this->transfers->transferInventories(
                postId: $post->id,
                postElementId: $element->id,
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
                'inventory' => 'Die gewählte Menge ist zu dieser Spielzeit nicht verfügbar oder wird für eine spätere Übergabe benötigt.',
            ]);
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'inventory' => 'Keine übertragbaren Gegenstände ausgewählt.',
            ]);
        }
    }

    private function resolveTransferParties(
        PostTransferData $data,
        Character $sender,
        ThreadScene $scene,
    ): array {
        if ($data->sourceType && $data->recipientType) {
            $sourceId = match ($data->sourceType) {
                PermissionEntityType::CHARACTER => $sender->id,
                PermissionEntityType::LOCATION => $scene->location_id,
                PermissionEntityType::COMPANY_SITE => $data->sourceId,
                default => null,
            };
            $recipientId = match ($data->recipientType) {
                PermissionEntityType::CHARACTER => $data->recipientId ?? $sender->id,
                PermissionEntityType::LOCATION => $scene->location_id,
                PermissionEntityType::COMPANY_SITE => $data->recipientId,
                default => null,
            };
            if (! $sourceId || ! $recipientId) {
                throw ValidationException::withMessages(['recipient_party' => 'Bitte wähle Quelle und Empfänger aus.']);
            }

            return [$data->sourceType, $sourceId, $data->recipientType, $recipientId, true];
        }

        return match ($data->action) {
            PostTransferAction::GIVE => [PermissionEntityType::CHARACTER, $sender->id, PermissionEntityType::CHARACTER, $data->recipientCharacterId, false],
            PostTransferAction::DROP => [PermissionEntityType::CHARACTER, $sender->id, PermissionEntityType::LOCATION, $scene->location_id, false],
            PostTransferAction::PICKUP => [PermissionEntityType::LOCATION, $scene->location_id, PermissionEntityType::CHARACTER, $sender->id, false],
            PostTransferAction::COMPANY_DEPOSIT => [PermissionEntityType::CHARACTER, $sender->id, PermissionEntityType::COMPANY_SITE, $data->companySiteId, false],
            PostTransferAction::COMPANY_WITHDRAWAL => [PermissionEntityType::COMPANY_SITE, $data->companySiteId, PermissionEntityType::CHARACTER, $data->recipientCharacterId, false],
            default => throw ValidationException::withMessages(['transfer_action' => 'Bitte wähle Quelle und Empfänger aus.']),
        };
    }

    private function createElement(Post $post, int $position, PostElementType $type): PostElement
    {
        return $post->elements()->create(['position' => $position, 'type' => $type]);
    }

    private function createMessageElement(Post $post, int $position, MessageElementData $data): PostElement
    {
        $element = $this->createElement($post, $position, PostElementType::MESSAGE);
        $element->message()->create(['message' => $data->message, 'smilies' => $data->smilies]);

        return $element;
    }

    private function createPollElement(Post $post, int $position, PollElementData $data, int $index): void
    {
        if ($data->question === '' || count($data->options) < 2) {
            throw ValidationException::withMessages(['elements.'.$index => 'Eine Umfrage benötigt eine Frage und mindestens zwei Antworten.']);
        }
        if ($data->maxChoices > count($data->options)) {
            throw ValidationException::withMessages(['elements.'.$index.'.max_choices' => 'Es können nicht mehr Antworten gewählt werden als vorhanden sind.']);
        }
        if ($data->closesAt !== null && $data->closesAt <= now()->timestamp) {
            throw ValidationException::withMessages(['elements.'.$index.'.closes_at' => 'Der Abstimmungsschluss muss in der Zukunft liegen.']);
        }

        $element = $this->createElement($post, $position, PostElementType::POLL);
        $poll = $element->poll()->create([
            'question' => $data->question,
            'visibility' => $data->visibility,
            'max_choices' => $data->maxChoices,
            'closes_at' => $data->closesAt,
        ]);
        foreach ($data->options as $optionPosition => $label) {
            $poll->options()->create(['position' => $optionPosition + 1, 'label' => $label]);
        }
    }

    private function createSceneTransitionElement(
        ForumThread $thread,
        Post $post,
        int $position,
        SceneTransitionElementData $data,
        ?ThreadScene $activeScene,
        ?string $activeSceneKey,
        User $user,
        int $index,
    ): array {
        $element = $this->createElement($post, $position, PostElementType::SCENE_TRANSITION);
        $endedScene = null;
        $startedScene = null;

        if ($data->action === 'end') {
            abort_unless($this->permissions->allows('endthreadscene', $thread, $user), 403);
            if (! $activeScene) {
                throw ValidationException::withMessages(['elements.'.$index => 'Es gibt an dieser Position keine Szene, die beendet werden könnte.']);
            }
        } elseif ($data->action === 'start') {
            abort_unless($this->permissions->allows('setthreadscene', $thread, $user), 403);
            if (! $data->locationId) {
                throw ValidationException::withMessages(['elements.'.$index.'.location_id' => 'Bitte wähle einen Ort für die Szene.']);
            }
        } else {
            throw ValidationException::withMessages(['elements.'.$index => 'Unbekannter Szenenwechsel.']);
        }

        if ($data->storyAt === null) {
            throw ValidationException::withMessages([
                'elements.'.$index.'.story_at' => 'Bitte gib die Spielzeit für den Szenenwechsel an.',
            ]);
        }

        if ($activeScene) {
            $activeScene->update([
                'ends_at_post_id' => $post->id,
                'story_ended_at' => $data->storyAt,
                'ended_at' => now(),
            ]);
            $endedScene = $activeScene;
            $activeScene = null;
            $activeSceneKey = null;
        }

        if ($data->action === 'start') {
            $startedScene = ThreadScene::create([
                'thread_id' => $thread->id,
                'location_id' => $data->locationId,
                'starts_at_post_id' => $post->id,
                'story_started_at' => $data->storyAt,
                'created_by_user_id' => $user->id,
            ]);
            $activeScene = $startedScene->load('location');
            $activeSceneKey = $data->sceneKey ?: 'draft-scene:'.$element->id;
        }

        PostSceneTransition::create([
            'post_element_id' => $element->id,
            'ended_scene_id' => $endedScene?->id,
            'started_scene_id' => $startedScene?->id,
        ]);

        return [$activeScene, $activeSceneKey];
    }

    private function userCharacter(User $user, int $characterId): Character
    {
        return $user->characters()
            ->whereKey($characterId)
            ->firstOrFail();
    }
}
