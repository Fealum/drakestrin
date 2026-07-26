<?php

namespace App\Services\Economy;

use App\Models\Economy\Inventory;
use App\Models\Economy\InventoryMutation;
use App\Models\Economy\Transfer;
use Illuminate\Support\Collection;

class InventoryConsistencyAuditor
{
    public function __construct(private InventoryMutationRecorder $mutationRecorder) {}

    /** @return list<string> */
    public function audit(array $transferIds = [], array $inventoryIds = []): array
    {
        $transfers = collect();

        if ($inventoryIds === [] || $transferIds !== []) {
            $transfers = Transfer::query()
                ->when($transferIds, fn ($query) => $query->whereIn('id', $transferIds))
                ->with(['items.item', 'post', 'reversalOf.items', 'scene'])
                ->orderBy('story_at')
                ->orderBy('id')
                ->get();
        }

        $issues = collect();

        foreach ($transfers as $transfer) {
            $this->auditContext($transfer, $issues);
            $this->auditReversal($transfer, $issues);
        }

        $this->auditInstances($transfers, $issues);
        $this->auditBalances($transfers, $issues);
        $this->auditMutationChains($transferIds, $inventoryIds, $issues);

        return $issues->unique()->values()->all();
    }

    private function auditMutationChains(array $transferIds, array $inventoryIds, Collection $issues): void
    {
        $auditedInventoryIds = collect($inventoryIds);

        if ($transferIds !== []) {
            $auditedInventoryIds = $auditedInventoryIds->merge(
                InventoryMutation::query()
                    ->where('source_type', 'transfer')
                    ->whereIn('source_id', $transferIds)
                    ->pluck('inventory_id'),
            );
        }

        $auditedInventoryIds = $auditedInventoryIds->filter()->unique()->values();
        $mutations = InventoryMutation::query()
            ->when(
                $auditedInventoryIds->isNotEmpty(),
                fn ($query) => $query->whereIn('inventory_id', $auditedInventoryIds),
            )
            ->when(
                ($transferIds !== [] || $inventoryIds !== []) && $auditedInventoryIds->isEmpty(),
                fn ($query) => $query->whereRaw('1 = 0'),
            )
            ->orderBy('inventory_id')
            ->orderBy('id')
            ->get()
            ->groupBy('inventory_id');
        $inventories = Inventory::query()->whereIn('id', $mutations->keys())->get()->keyBy('id');

        foreach ($mutations as $inventoryId => $chain) {
            $previous = null;

            foreach ($chain as $mutation) {
                if ($mutation->before_state === null && $mutation->after_state === null) {
                    $issues->push("Inventory mutation #{$mutation->id} has neither a before nor an after state.");
                }

                if (($mutation->before_state['item_id'] ?? $mutation->item_id) !== $mutation->item_id
                    || ($mutation->after_state['item_id'] ?? $mutation->item_id) !== $mutation->item_id) {
                    $issues->push("Inventory mutation #{$mutation->id} item differs from its recorded state.");
                }

                if ($previous && ! $this->statesMatch($previous->after_state, $mutation->before_state)) {
                    $issues->push("Inventory #{$inventoryId} changed without a mutation before mutation #{$mutation->id}.");
                }

                $previous = $mutation;
            }

            $latestState = $this->normalizeState($previous?->after_state);
            $inventory = $inventories->get($inventoryId);
            $currentState = $inventory ? $this->normalizeState($this->mutationRecorder->snapshot($inventory)) : null;

            if (! $this->statesMatch($latestState, $currentState)) {
                $issues->push("Inventory #{$inventoryId} live state differs from its latest mutation.");
            }
        }
    }

    private function statesMatch(?array $left, ?array $right): bool
    {
        return $this->normalizeState($left) === $this->normalizeState($right);
    }

    /** @return array{item_id:int,stack:int,wear:int,owner_type:int,owner_id:int,timelastvalue:int,data:string}|null */
    private function normalizeState(?array $state): ?array
    {
        if ($state === null) {
            return null;
        }

        return [
            'item_id' => (int) ($state['item_id'] ?? 0),
            'stack' => (int) ($state['stack'] ?? 0),
            'wear' => (int) ($state['wear'] ?? 0),
            'owner_type' => (int) ($state['owner_type'] ?? 0),
            'owner_id' => (int) ($state['owner_id'] ?? 0),
            'timelastvalue' => (int) ($state['timelastvalue'] ?? 0),
            'data' => (string) ($state['data'] ?? ''),
        ];
    }

    private function auditContext(Transfer $transfer, Collection $issues): void
    {
        if (! $transfer->scene || ! $transfer->post) {
            $issues->push("Transfer #{$transfer->id} has no valid scene or post.");

            return;
        }

        if ($transfer->story_at !== $transfer->scene->story_started_at) {
            $issues->push("Transfer #{$transfer->id} story time differs from scene #{$transfer->thread_scene_id}.");
        }

        if ($transfer->post->thread_id !== $transfer->scene->thread_id) {
            $issues->push("Transfer #{$transfer->id} post and scene belong to different threads.");
        }
    }

    private function auditReversal(Transfer $transfer, Collection $issues): void
    {
        if (! $transfer->reversal_of_transfer_id) {
            return;
        }

        $original = $transfer->reversalOf;

        if (! $original) {
            $issues->push("Reversal #{$transfer->id} references a missing transfer.");

            return;
        }

        if ($transfer->sender_type !== $original->recipient_type
            || $transfer->sender_id !== $original->recipient_id
            || $transfer->recipient_type !== $original->sender_type
            || $transfer->recipient_id !== $original->sender_id) {
            $issues->push("Reversal #{$transfer->id} does not invert transfer #{$original->id} participants.");
        }

        $originalItems = $original->items->map(fn ($item) => [$item->item_id, $item->inventory_id, max(1, $item->stack)])->sort()->values();
        $reversalItems = $transfer->items->map(fn ($item) => [$item->item_id, $item->inventory_id, max(1, $item->stack)])->sort()->values();

        if ($originalItems->all() !== $reversalItems->all()) {
            $issues->push("Reversal #{$transfer->id} items differ from transfer #{$original->id}.");
        }
    }

    private function auditInstances(Collection $transfers, Collection $issues): void
    {
        $instanceEvents = $transfers
            ->flatMap(fn (Transfer $transfer) => $transfer->items
                ->whereNotNull('inventory_id')
                ->map(fn ($item) => ['transfer' => $transfer, 'inventory_id' => $item->inventory_id]))
            ->groupBy('inventory_id');

        foreach ($instanceEvents as $inventoryId => $events) {
            $latest = $events->last()['transfer'];
            $inventory = Inventory::find($inventoryId);

            if (! $inventory) {
                $issues->push("Item instance #{$inventoryId} referenced by the ledger is missing.");
            } elseif ($inventory->owner_type !== $latest->recipient_type || $inventory->owner_id !== $latest->recipient_id) {
                $issues->push("Item instance #{$inventoryId} current owner differs from transfer #{$latest->id}.");
            }
        }
    }

    private function auditBalances(Collection $transfers, Collection $issues): void
    {
        $events = collect();

        foreach ($transfers as $transfer) {
            foreach ($transfer->items as $item) {
                $quantity = max(1, (int) $item->stack);

                if ($transfer->sender_type !== null && $transfer->sender_id !== null) {
                    $events->push($this->balanceEvent($transfer, $item->item_id, $transfer->sender_type, $transfer->sender_id, -$quantity));
                }

                if ($transfer->recipient_type !== null && $transfer->recipient_id !== null) {
                    $events->push($this->balanceEvent($transfer, $item->item_id, $transfer->recipient_type, $transfer->recipient_id, $quantity));
                }
            }
        }

        foreach ($events->groupBy('key') as $key => $ownerEvents) {
            [$type, $ownerId, $itemId] = array_map('intval', explode(':', $key));
            $current = Inventory::query()
                ->with('item')
                ->where('owner_type', $type)
                ->where('owner_id', $ownerId)
                ->where('item_id', $itemId)
                ->get()
                ->sum(fn (Inventory $inventory) => $inventory->item?->stackable ? (int) $inventory->stack : 1);
            $balance = $current - $ownerEvents->sum('change');

            foreach ($ownerEvents as $event) {
                $balance += $event['change'];

                if ($balance < 0) {
                    $issues->push("Negative balance for owner {$type}:{$ownerId}, item #{$itemId}, at transfer #{$event['transfer_id']}.");
                    break;
                }
            }
        }
    }

    private function balanceEvent(Transfer $transfer, int $itemId, int $type, int $ownerId, int $change): array
    {
        return [
            'key' => "{$type}:{$ownerId}:{$itemId}",
            'story_at' => $transfer->story_at,
            'transfer_id' => $transfer->id,
            'change' => $change,
        ];
    }
}
