<?php

namespace App\Services\Economy;

use App\Data\Economy\InventoryOwner;
use App\Exceptions\Economy\InventoryUnavailableAtStoryTime;
use App\Models\Economy\Inventory;
use App\Models\Economy\InventoryMutation;
use App\Models\Economy\Item;
use App\Models\Economy\Transfer;
use App\Support\InventoryMutationClock;
use Illuminate\Support\Collection;

class InventoryTimeline
{
    /**
     * Return current inventory rows capped to quantities that can be removed at
     * the story time without making this owner's later balance negative.
     *
     * @return Collection<int, Inventory>
     */
    public function transferableInventory(InventoryOwner $owner, int $storyAt): Collection
    {
        $inventories = Inventory::query()
            ->with('item')
            ->ownedBy($owner->type, $owner->id)
            ->orderBy('id')
            ->get();
        $remainingByItem = $inventories
            ->filter(fn (Inventory $inventory) => (bool) $inventory->item?->stackable)
            ->pluck('item_id')
            ->unique()
            ->mapWithKeys(fn (int $itemId) => [
                $itemId => $this->transferableQuantity($owner, $itemId, $storyAt),
            ]);

        return $inventories
            ->filter(function (Inventory $inventory) use ($remainingByItem, $owner, $storyAt) {
                if ($inventory->item && ! $inventory->item->stackable) {
                    return $this->instanceTransferableQuantity($owner, $inventory->id, $storyAt) > 0;
                }

                $remaining = (int) $remainingByItem->get($inventory->item_id, 0);

                if ($remaining <= 0 || ! $inventory->item) {
                    return false;
                }

                if ($inventory->item->stackable) {
                    $shown = min($remaining, (int) $inventory->stack);
                    $inventory->stack = $shown;
                    $remainingByItem->put($inventory->item_id, $remaining - $shown);
                } else {
                    $remainingByItem->put($inventory->item_id, $remaining - 1);
                }

                return true;
            })
            ->values();
    }

    /**
     * @param  array<int, int>  $quantitiesByItemId
     */
    public function assertRemovable(InventoryOwner $owner, int $storyAt, array $quantitiesByItemId): void
    {
        foreach ($quantitiesByItemId as $itemId => $quantity) {
            if ($quantity > $this->transferableQuantity($owner, (int) $itemId, $storyAt)) {
                throw new InventoryUnavailableAtStoryTime(
                    'The selected quantity is unavailable at this story time or is required by a later transfer.'
                );
            }
        }
    }

    public function assertInstanceRemovable(InventoryOwner $owner, int $inventoryId, int $storyAt): void
    {
        if ($this->instanceTransferableQuantity($owner, $inventoryId, $storyAt) < 1) {
            throw new InventoryUnavailableAtStoryTime(
                'The selected item instance is unavailable at this story time or is required by a later transfer.'
            );
        }
    }

    public function transferableQuantity(InventoryOwner $owner, int $itemId, int $storyAt): int
    {
        $current = Inventory::query()
            ->with('item')
            ->ownedBy($owner->type, $owner->id)
            ->where('item_id', $itemId)
            ->get()
            ->sum(fn (Inventory $inventory) => $inventory->item?->stackable ? (int) $inventory->stack : 1);
        $events = $this->events($owner, $itemId);
        $balance = $current - $events->sum('change');

        foreach ($events as $event) {
            if ($event['story_at'] > $storyAt) {
                break;
            }

            $balance += $event['change'];
        }

        $minimum = $balance;

        foreach ($events as $event) {
            if ($event['story_at'] <= $storyAt) {
                continue;
            }

            $balance += $event['change'];
            $minimum = min($minimum, $balance);
        }

        return max(0, $minimum);
    }

    private function instanceTransferableQuantity(InventoryOwner $owner, int $inventoryId, int $storyAt): int
    {
        $current = Inventory::query()
            ->whereKey($inventoryId)
            ->ownedBy($owner->type, $owner->id)
            ->exists() ? 1 : 0;
        $events = $this->instanceEvents($owner, $inventoryId);
        $balance = $current - $events->sum('change');

        foreach ($events as $event) {
            if ($event['story_at'] > $storyAt) {
                break;
            }

            $balance += $event['change'];
        }

        $minimum = $balance;

        foreach ($events as $event) {
            if ($event['story_at'] <= $storyAt) {
                continue;
            }

            $balance += $event['change'];
            $minimum = min($minimum, $balance);
        }

        return max(0, $minimum);
    }

    /**
     * @return Collection<int, array{story_at:int, event_order:int, change:int}>
     */
    private function events(InventoryOwner $owner, int $itemId): Collection
    {
        $ownerType = $owner->type->value;

        $transferEvents = Transfer::query()
            ->select(['id', 'story_at', 'sender_type', 'sender_id', 'recipient_type', 'recipient_id'])
            ->with(['items' => fn ($query) => $query->where('item_id', $itemId)])
            ->whereNotNull('story_at')
            ->where(function ($query) use ($owner, $ownerType) {
                $query->where(function ($query) use ($owner, $ownerType) {
                    $query->where('sender_type', $ownerType)->where('sender_id', $owner->id);
                })->orWhere(function ($query) use ($owner, $ownerType) {
                    $query->where('recipient_type', $ownerType)->where('recipient_id', $owner->id);
                });
            })
            ->orderBy('story_at')
            ->orderBy('id')
            ->get()
            ->flatMap(function (Transfer $transfer) use ($owner, $ownerType) {
                $direction = $transfer->recipient_type === $ownerType && $transfer->recipient_id === $owner->id ? 1 : -1;

                return $transfer->items->map(fn ($item) => [
                    'story_at' => (int) $transfer->story_at,
                    'event_order' => (int) $transfer->id,
                    'change' => $direction * max(1, (int) $item->stack),
                ]);
            })
            ->values();

        $stackable = (bool) Item::query()->whereKey($itemId)->value('stackable');
        $simulationEvents = InventoryMutation::query()
            ->where('item_id', $itemId)
            ->where('clock', InventoryMutationClock::SIMULATION->value)
            ->orderBy('effective_at')
            ->orderBy('id')
            ->get()
            ->map(function (InventoryMutation $mutation) use ($owner, $stackable) {
                $before = $this->stateQuantity($mutation->before_state, $owner, $stackable);
                $after = $this->stateQuantity($mutation->after_state, $owner, $stackable);

                return [
                    'story_at' => (int) $mutation->effective_at,
                    'event_order' => 1_000_000_000 + (int) $mutation->id,
                    'change' => $after - $before,
                ];
            })
            ->filter(fn (array $event) => $event['change'] !== 0);

        return $transferEvents
            ->concat($simulationEvents)
            ->sortBy(fn (array $event) => [$event['story_at'], $event['event_order']])
            ->values();
    }

    private function instanceEvents(InventoryOwner $owner, int $inventoryId): Collection
    {
        $ownerType = $owner->type->value;

        $transferEvents = Transfer::query()
            ->select(['id', 'story_at', 'sender_type', 'sender_id', 'recipient_type', 'recipient_id'])
            ->whereNotNull('story_at')
            ->whereHas('items', fn ($query) => $query->where('inventory_id', $inventoryId))
            ->where(function ($query) use ($owner, $ownerType) {
                $query->where(function ($query) use ($owner, $ownerType) {
                    $query->where('sender_type', $ownerType)->where('sender_id', $owner->id);
                })->orWhere(function ($query) use ($owner, $ownerType) {
                    $query->where('recipient_type', $ownerType)->where('recipient_id', $owner->id);
                });
            })
            ->orderBy('story_at')
            ->orderBy('id')
            ->get()
            ->map(fn (Transfer $transfer) => [
                'story_at' => (int) $transfer->story_at,
                'event_order' => (int) $transfer->id,
                'change' => $transfer->recipient_type === $ownerType && $transfer->recipient_id === $owner->id ? 1 : -1,
            ]);

        $simulationEvents = InventoryMutation::query()
            ->where('inventory_id', $inventoryId)
            ->where('clock', InventoryMutationClock::SIMULATION->value)
            ->orderBy('effective_at')
            ->orderBy('id')
            ->get()
            ->map(function (InventoryMutation $mutation) use ($owner) {
                $before = $this->stateBelongsToOwner($mutation->before_state, $owner) ? 1 : 0;
                $after = $this->stateBelongsToOwner($mutation->after_state, $owner) ? 1 : 0;

                return [
                    'story_at' => (int) $mutation->effective_at,
                    'event_order' => 1_000_000_000 + (int) $mutation->id,
                    'change' => $after - $before,
                ];
            })
            ->filter(fn (array $event) => $event['change'] !== 0);

        return $transferEvents
            ->concat($simulationEvents)
            ->sortBy(fn (array $event) => [$event['story_at'], $event['event_order']])
            ->values();
    }

    private function stateQuantity(?array $state, InventoryOwner $owner, bool $stackable): int
    {
        if (! $this->stateBelongsToOwner($state, $owner)) {
            return 0;
        }

        return $stackable ? max(0, (int) ($state['stack'] ?? 0)) : 1;
    }

    private function stateBelongsToOwner(?array $state, InventoryOwner $owner): bool
    {
        return $state !== null
            && (int) ($state['owner_type'] ?? -1) === $owner->type->value
            && (int) ($state['owner_id'] ?? 0) === $owner->id;
    }
}
