<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Item;

class InventoryService
{
    public function available(int $itemId, int $ownerTable, int $ownerId, int $wear): int
    {
        return $this->inventories($itemId, $ownerTable, $ownerId, $wear)
            ->get()
            ->sum(fn (Inventory $inventory) => $inventory->itemModel?->stackable ? (int) $inventory->stack : 1);
    }

    public function debitStack(int $itemId, int $amount, int $ownerTable, int $ownerId, int $wear): int
    {
        $remaining = max(0, $amount);
        $paid = 0;

        foreach ($this->inventories($itemId, $ownerTable, $ownerId, $wear, lock: true)->get() as $inventory) {
            if ($remaining <= 0) {
                break;
            }

            $available = (int) $inventory->stack;
            $take = min($remaining, $available);

            if ($take <= 0) {
                continue;
            }

            $remaining -= $take;
            $paid += $take;

            if ($take === $available) {
                $inventory->update(['stack' => 0]);
            } else {
                $inventory->decrement('stack', $take);
            }
        }

        return $paid;
    }

    public function add(int $itemId, int $quantity, int $ownerTable, int $ownerId, int $wear): int
    {
        $quantity = max(0, $quantity);
        $item = Item::find($itemId);

        if (! $item || $quantity === 0) {
            return 0;
        }

        if ($item->stackable) {
            $target = $this->inventory($itemId, $ownerTable, $ownerId, $wear, lock: true);

            if ($target) {
                $target->increment('stack', $quantity);
            } else {
                Inventory::create([
                    'item' => $itemId,
                    'stack' => $quantity,
                    'wear' => $wear,
                    'table__owner' => $ownerTable,
                    'owner' => $ownerId,
                    'timelastvalue' => 0,
                    'data' => '',
                ]);
            }

            return $quantity;
        }

        for ($i = 0; $i < $quantity; $i++) {
            Inventory::create([
                'item' => $itemId,
                'stack' => 0,
                'wear' => $wear,
                'table__owner' => $ownerTable,
                'owner' => $ownerId,
                'timelastvalue' => 0,
                'data' => '',
            ]);
        }

        return $quantity;
    }

    public function take(int $itemId, int $quantity, int $ownerTable, int $ownerId, int $fromWear, ?int $toWear = null): int
    {
        $remaining = max(0, $quantity);
        $taken = 0;

        foreach ($this->inventories($itemId, $ownerTable, $ownerId, $fromWear, lock: true)->get() as $inventory) {
            if ($remaining <= 0) {
                break;
            }

            if ($inventory->itemModel?->stackable) {
                $amount = min($remaining, (int) $inventory->stack);
                $remaining -= $amount;
                $taken += $amount;

                if ($toWear !== null) {
                    $this->createOrIncrement($itemId, $amount, $ownerTable, $ownerId, $toWear, $inventory);
                }

                if ((int) $inventory->stack === $amount) {
                    $inventory->delete();
                } else {
                    $inventory->decrement('stack', $amount);
                }

                continue;
            }

            $remaining--;
            $taken++;

            if ($toWear !== null) {
                $inventory->update(['wear' => $toWear]);
            } else {
                $inventory->delete();
            }
        }

        return $taken;
    }

    /**
     * @return array{0:int,1:int}
     */
    public function moveInventory(Inventory $inventory, int $toOwnerTable, int $toOwnerId, int $toWear, int|string|null $requestedStack = null): array
    {
        $inventory->loadMissing('itemModel');
        $item = $inventory->itemModel;

        if (! $item) {
            return [(int) $inventory->item, 0];
        }

        if (! $item->stackable) {
            $inventory->update([
                'table__owner' => $toOwnerTable,
                'owner' => $toOwnerId,
                'wear' => $toWear,
            ]);

            return [$item->id, 0];
        }

        $stack = $inventory->undounitary($requestedStack ?? 1);

        if ($stack <= 0) {
            $stack = 1;
        } elseif ($stack > $inventory->stack) {
            $stack = $inventory->stack;
        }

        $target = $this->inventory($item->id, $toOwnerTable, $toOwnerId, $toWear);

        if ($stack === (int) $inventory->stack && ! $target) {
            $inventory->update([
                'table__owner' => $toOwnerTable,
                'owner' => $toOwnerId,
                'wear' => $toWear,
            ]);
        } elseif ($stack === (int) $inventory->stack && $target) {
            $target->increment('stack', $stack);
            $inventory->delete();
        } else {
            if ($target) {
                $target->increment('stack', $stack);
            } else {
                Inventory::create([
                    'item' => $item->id,
                    'stack' => $stack,
                    'wear' => $toWear,
                    'table__owner' => $toOwnerTable,
                    'owner' => $toOwnerId,
                    'timelastvalue' => 0,
                    'data' => '',
                ]);
            }

            $inventory->decrement('stack', $stack);
        }

        return [$item->id, $stack];
    }

    private function createOrIncrement(int $itemId, int $amount, int $ownerTable, int $ownerId, int $wear, Inventory $source): void
    {
        $target = $this->inventory($itemId, $ownerTable, $ownerId, $wear, lock: true);

        if ($target) {
            $target->increment('stack', $amount);

            return;
        }

        Inventory::create([
            'item' => $itemId,
            'stack' => $amount,
            'wear' => $wear,
            'table__owner' => $ownerTable,
            'owner' => $ownerId,
            'timelastvalue' => $source->timelastvalue ?? 0,
            'data' => $source->data ?? '',
        ]);
    }

    private function inventory(int $itemId, int $ownerTable, int $ownerId, int $wear, bool $lock = false): ?Inventory
    {
        return $this->inventories($itemId, $ownerTable, $ownerId, $wear, $lock)->first();
    }

    private function inventories(int $itemId, int $ownerTable, int $ownerId, int $wear, bool $lock = false)
    {
        $query = Inventory::query()
            ->with('itemModel')
            ->where('item', $itemId)
            ->where('table__owner', $ownerTable)
            ->where('owner', $ownerId)
            ->where('wear', $wear)
            ->orderBy('id');

        return $lock ? $query->lockForUpdate() : $query;
    }
}
