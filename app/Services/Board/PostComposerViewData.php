<?php

namespace App\Services\Board;

use App\Data\Economy\InventoryOwner;
use App\Models\Board\Board;
use App\Models\Board\PostDraft;
use App\Models\Board\Thread;
use App\Models\Economy\CompanySite;
use App\Models\Territory\Location;
use App\Models\User;
use App\Repositories\Territory\LocationRepository;
use App\Services\Economy\InventoryTimeline;
use App\Services\PermissionService;
use App\Support\InventoryStockState;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\Gate;

class PostComposerViewData
{
    public function __construct(
        private PostDraftService $drafts,
        private InventoryTimeline $inventoryTimeline,
        private LocationRepository $locations,
        private PermissionService $permissions,
    ) {}

    public function make(PostDraft $draft, User $user, ?Thread $thread): array
    {
        $characters = $user->characters()->with('inventory.item')->orderBy('name')->get();
        $permissionTarget = $thread ?? new Thread(['board_id' => $draft->board_id ?? 0]);

        return [
            'boards' => Board::query()->where('cat', 0)->orderBy('sort')->orderBy('name')->get()
                ->filter(fn (Board $board) => Gate::allows('view', $board) && Gate::allows('createThread', $board)),
            'canCreatePoll' => $this->permissions->allows('createpoll', $permissionTarget, $user),
            'canEndScene' => $this->permissions->allows('endthreadscene', $permissionTarget, $user),
            'canSetScene' => $this->permissions->allows('setthreadscene', $permissionTarget, $user),
            'canTransfer' => $this->permissions->allows('transfer', $permissionTarget, $user),
            'characters' => $characters,
            'draft' => $draft,
            'endsInsideScene' => $this->drafts->endsInsideScene($draft->payload, $thread),
            'locations' => Location::query()->orderBy('priority')->orderByRaw('LOWER(name)')->get(),
            'moveTargets' => $this->drafts->validMoveTargets($draft->payload, $thread),
            'thread' => $thread,
            'transferContexts' => $this->transferContexts($draft, $user, $characters),
        ];
    }

    private function transferContexts(PostDraft $draft, User $user, $characters): array
    {
        $scene = $draft->thread?->currentScene;
        $locationId = $scene?->location_id;
        $storyAt = $scene?->story_started_at;
        $contexts = [];

        foreach ($draft->payload as $index => $element) {
            if (($element['type'] ?? null) === 'scene_transition') {
                if (($element['scene_action'] ?? 'start') === 'end') {
                    $locationId = null;
                    $storyAt = null;
                } else {
                    $locationId = filled($element['location_id'] ?? null) ? (int) $element['location_id'] : null;
                    $storyAt = filled($element['story_at'] ?? null) ? (int) $element['story_at'] : null;
                }
            }
            if (($element['type'] ?? null) !== 'transfer' || ! $locationId || $storyAt === null) {
                continue;
            }

            $location = Location::find($locationId);
            if (! $location) {
                continue;
            }
            $locationInventory = $this->inventoryTimeline->transferableInventory(
                new InventoryOwner(PermissionEntityType::LOCATION, $locationId), $storyAt,
            );
            $sites = CompanySite::query()
                ->with(['company.sites:id,company_id,name', 'inventory.item'])
                ->whereIn('location_id', $this->locations->ancestorLocationIds($location))
                ->orderBy('company_id')->orderBy('name')->get();
            $sites->each(function (CompanySite $site) use ($storyAt, $user, $characters) {
                $canWithdraw = $characters->contains(fn ($character) => $user->can('transfer', [$site, $character]));
                $inventory = $canWithdraw
                    ? $this->inventoryTimeline->transferableInventory(
                        new InventoryOwner(PermissionEntityType::COMPANY_SITE, $site->id), $storyAt,
                    )->filter(fn ($inventory) => (int) $inventory->wear >= InventoryStockState::RESERVED->value)->values()
                    : collect();
                $site->setRelation('inventory', $inventory);
            });
            $contexts[$index] = compact('locationInventory', 'sites');
        }

        return $contexts;
    }
}
