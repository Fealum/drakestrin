<?php

namespace App\Services\Board;

use App\Data\Economy\InventoryOwner;
use App\Models\Board\Board;
use App\Models\Board\PostDraft;
use App\Models\Board\Thread;
use App\Models\Economy\CompanySite;
use App\Models\Territory\Location;
use App\Models\User;
use App\Models\User\Character;
use App\Repositories\Board\ThreadParticipantRepository;
use App\Repositories\Territory\LocationRepository;
use App\Services\Economy\InventoryTimeline;
use App\Services\PermissionService;
use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class PostComposerViewData
{
    public function __construct(
        private PostDraftService $drafts,
        private InventoryTimeline $inventoryTimeline,
        private LocationRepository $locations,
        private PermissionService $permissions,
        private ThreadParticipantRepository $participants,
        private PostComposerPreview $previews,
    ) {}

    public function make(PostDraft $draft, User $user, ?Thread $thread): array
    {
        $draft = clone $draft;
        $draft->payload = $this->drafts->withTextSlots((array) request()->old('elements', $draft->payload));
        $characters = $thread
            ? $this->participants->postingCharacters($user, $thread)
            : $user->characters()->orderByRaw('LOWER(name)')->get();
        $permissionTarget = $thread ?? new Thread(['board_id' => $draft->board_id ?? 0]);
        $sceneStates = $this->drafts->sceneStates($draft->payload, $thread);
        $moveTargets = $this->drafts->validMoveTargets($draft->payload, $thread);
        $transferContexts = $this->transferContexts($draft, $user, $characters, $sceneStates['elements']);
        $transferPickers = [];
        foreach ($draft->payload as $index => $element) {
            if (($element['type'] ?? null) === 'transfer') {
                $transferPickers[$index] = $this->transferPickers($transferContexts[$index] ?? [], $element, $characters->firstWhere('id', $draft->character_id));
            }
        }

        $locationIds = collect($draft->payload)->pluck('location_id')
            ->merge(collect($sceneStates['elements'])->pluck('location_id'))->filter()->unique();
        $locations = Location::query()->whereKey($locationIds)->get();
        $elementPreviews = [];
        $locationChoices = [];
        foreach ($draft->payload as $index => $element) {
            if (($element['type'] ?? null) === 'scene_transition') {
                $locationChoices[$index] = $this->locations->composerChoices((string) ($element['location_query'] ?? ''), (int) ($element['location_page'] ?? 1), (int) ($element['location_id'] ?? 0) ?: null);
            }
            $elementPreviews[$index] = $this->previews->make($element, $sceneStates['elements'][$index] ?? [],
                $transferContexts[$index] ?? [], $transferPickers[$index] ?? [], $draft->character_id, $locations);
        }

        return [
            'boards' => $thread ? collect() : Board::query()->where('cat', 0)->orderBy('sort')->orderBy('name')->get()
                ->filter(fn (Board $board) => Gate::allows('view', $board) && Gate::allows('createThread', $board)),
            'canCreatePoll' => $this->permissions->allows('createpoll', $permissionTarget, $user),
            'canCreateCharacter' => $this->permissions->allows('createcharacter', $permissionTarget, $user),
            'canEndScene' => $this->permissions->allows('endthreadscene', $permissionTarget, $user),
            'canSetScene' => $this->permissions->allows('setthreadscene', $permissionTarget, $user),
            'canTransfer' => $this->permissions->allows('transfer', $permissionTarget, $user),
            'characters' => $characters,
            'draft' => $draft,
            'endsInsideScene' => $this->drafts->endsInsideScene($draft->payload, $thread),
            'elementSceneStates' => $sceneStates['elements'],
            'elementPreviews' => $elementPreviews,
            'initialScene' => $thread?->currentScene,
            'insertedElementKeys' => request()->attributes->get('composer.inserted_keys', session('composer_inserted_keys', [])),
            'locations' => $locations,
            'locationChoices' => $locationChoices,
            'markerOptions' => $this->drafts->markerOptions($draft->payload, $thread),
            'moveTargets' => $moveTargets,
            'removableElements' => $this->drafts->removableElements($draft->payload, $thread),
            'sceneActionOptions' => $this->drafts->validSceneActions($draft->payload, $thread),
            'thread' => $thread,
            'transferContexts' => $transferContexts,
            'transferPickers' => $transferPickers,
        ];
    }

    private function transferContexts(PostDraft $draft, User $user, $characters, array $states): array
    {
        $contexts = [];
        $byScene = [];
        $recipients = null;

        foreach ($draft->payload as $index => $element) {
            $locationId = $states[$index]['location_id'] ?? null;
            $storyAt = $states[$index]['story_at'] ?? null;
            if (($element['type'] ?? null) !== 'transfer' || ! $locationId || $storyAt === null) {
                continue;
            }

            $contextKey = $locationId.':'.$storyAt;
            if (isset($byScene[$contextKey])) {
                $contexts[$index] = $byScene[$contextKey];

                continue;
            }

            $location = Location::find($locationId);
            if (! $location) {
                continue;
            }
            $locationInventory = $this->inventoryTimeline->transferableInventory(
                new InventoryOwner(PermissionEntityType::LOCATION, $locationId), $storyAt,
            );
            $characterInventories = $characters->mapWithKeys(fn ($character) => [
                $character->id => $this->inventoryTimeline->transferableInventory(
                    new InventoryOwner(PermissionEntityType::CHARACTER, $character->id), $storyAt,
                ),
            ]);
            $sites = CompanySite::query()
                ->with('company.sites:id,company_id,name')
                ->whereIn('location_id', $this->locations->ancestorLocationIds($location))
                ->orderBy('company_id')->orderBy('name')->get();
            $sites->each(function (CompanySite $site) use ($storyAt, $user, $characters) {
                $authorizedCharacterIds = $characters
                    ->filter(fn ($character) => $user->can('transfer', [$site, $character]))
                    ->pluck('id')->values();
                $inventory = $authorizedCharacterIds->isNotEmpty()
                    ? $this->inventoryTimeline->transferableInventory(
                        new InventoryOwner(PermissionEntityType::COMPANY_SITE, $site->id), $storyAt,
                    )->filter(fn ($inventory) => (int) $inventory->wear >= InventoryStockState::RESERVED->value)->values()
                    : collect();
                $site->setRelation('inventory', $inventory);
                $site->setAttribute('authorized_character_ids', $authorizedCharacterIds->all());
            });
            $recipients ??= $draft->thread
                ? $this->participants->eligibleRecipients($draft->thread, $draft->character_id)
                : collect();
            $options = $this->partyOptions($characters->firstWhere('id', $draft->character_id), $location, $recipients, $sites);
            $contexts[$index] = [...compact('characterInventories', 'location', 'locationInventory', 'recipients', 'sites'), ...$options];
            $byScene[$contextKey] = $contexts[$index];
        }

        return $contexts;
    }

    private function partyOptions(?Character $character, Location $location, Collection $recipients, Collection $sites): array
    {
        $self = ['value' => 'character', 'label' => $character?->name ?? 'Neuer Charakter',
            'avatar' => $character?->avatarThumbUrl(), 'icon' => 'user', 'current_character' => true];
        $place = ['value' => 'location:'.$location->id, 'label' => $location->name, 'icon' => 'map-marker'];
        $companies = $sites->map(fn (CompanySite $site) => [
            'value' => 'company_site:'.$site->id,
            'label' => $site->company?->name.($site->company?->sites->count() > 1 ? ' ('.$site->name.')' : ''),
            'icon' => 'building', 'character_ids' => $site->authorized_character_ids ?? [],
        ]);

        return [
            'sourceOptions' => [$self, ...$companies->filter(fn (array $option) => $option['character_ids'] !== [])
                ->map(fn (array $option) => [...$option, 'disabled' => ! in_array($character?->id, $option['character_ids'], true)])->values()->all(), $place],
            'recipientOptions' => [$self, ...$recipients->map(fn (Character $recipient) => [
                'value' => 'character:'.$recipient->id, 'label' => $recipient->name, 'avatar' => $recipient->avatarThumbUrl(),
            ])->all(), ...$companies->all(), $place],
        ];
    }

    private function transferPickers(array $context, array $element, ?Character $character): array
    {
        $self = ['value' => 'character', 'label' => $character?->name ?? 'Neuer Charakter', 'avatar' => $character?->avatarThumbUrl(), 'icon' => 'user', 'current_character' => true];
        $legacyAction = $element['transfer_action'] ?? null;
        $sourceValue = $element['source'] ?? match ($legacyAction) {
            'pickup' => 'location:'.(($context['location'] ?? null)?->id ?? 0),
            'company_withdrawal' => 'company_site:'.($element['company_site'] ?? 0),
            default => 'character',
        };
        $sourceOptions = $context['sourceOptions'] ?? [$self];
        $stockCharacterIds = ($context['characterInventories'] ?? collect())
            ->filter(fn (Collection $inventory) => $inventory->isNotEmpty())->keys()->all();
        $sourceOptions = array_map(fn (array $option) => $option['value'] === 'character'
            ? [...$option, 'stock_character_ids' => $stockCharacterIds, 'disabled' => ! in_array($character?->id, $stockCharacterIds, true)]
            : $option, $sourceOptions);
        $source = collect($sourceOptions)->firstWhere('value', $sourceValue);
        if (! $source || ($source['disabled'] ?? false)) {
            $sourceValue = in_array($character?->id, $stockCharacterIds, true) ? 'character' : '';
        }
        $recipientValue = $element['recipient_party'] ?? match ($legacyAction) {
            'drop' => 'location:'.(($context['location'] ?? null)?->id ?? 0),
            'pickup' => 'character',
            'company_deposit' => 'company_site:'.($element['company_site'] ?? 0),
            'give', 'company_withdrawal' => filled($element['recipient'] ?? null) ? 'character:'.$element['recipient'] : '',
            default => '',
        };
        $recipientOptions = collect($context['recipientOptions'] ?? [$self])->map(function (array $option) use ($sourceValue, $character) {
            $isSelf = $option['value'] === 'character' || $option['value'] === 'character:'.$character?->id;
            $option['disabled'] = $sourceValue === '' || (str_starts_with($sourceValue, 'location:')
                ? $option['value'] !== 'character'
                : ($sourceValue === 'character' && $isSelf)
                    || ($sourceValue === $option['value'])
                    || ($option['value'] === 'character:'.$character?->id));

            return $option;
        })->all();
        if (str_starts_with($sourceValue, 'location:')) {
            $recipientValue = 'character';
        }

        return compact('sourceValue', 'sourceOptions', 'recipientValue', 'recipientOptions');
    }
}
