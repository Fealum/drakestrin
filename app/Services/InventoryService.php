<?php

namespace App\Services;

use App\Models\Economy\Inventory;
use App\Models\Economy\Item;
use App\Support\PermissionEntityType;

class InventoryService
{
    public function available(int $itemId, PermissionEntityType $ownerType, int $ownerId, int $wear): int
    {
        return $this->inventories($itemId, $ownerType, $ownerId, $wear)
            ->get()
            ->sum(fn (Inventory $inventory) => $inventory->item?->stackable ? (int) $inventory->stack : 1);
    }

    public function debitStack(int $itemId, int $amount, PermissionEntityType $ownerType, int $ownerId, int $wear): int
    {
        $remaining = max(0, $amount);
        $paid = 0;

        foreach ($this->inventories($itemId, $ownerType, $ownerId, $wear, lock: true)->get() as $inventory) {
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

    public function add(int $itemId, int $quantity, PermissionEntityType $ownerType, int $ownerId, int $wear): int
    {
        $quantity = max(0, $quantity);
        $item = Item::find($itemId);

        if (! $item || $quantity === 0) {
            return 0;
        }

        if ($item->stackable) {
            $target = $this->inventory($itemId, $ownerType, $ownerId, $wear, lock: true);

            if ($target) {
                $target->increment('stack', $quantity);
            } else {
                Inventory::create([
                    'item_id' => $itemId,
                    'stack' => $quantity,
                    'wear' => $wear,
                    'owner_type' => $ownerType->value,
                    'owner_id' => $ownerId,
                    'timelastvalue' => 0,
                    'data' => '',
                ]);
            }

            return $quantity;
        }

        for ($i = 0; $i < $quantity; $i++) {
            Inventory::create([
                'item_id' => $itemId,
                'stack' => 0,
                'wear' => $wear,
                'owner_type' => $ownerType->value,
                'owner_id' => $ownerId,
                'timelastvalue' => 0,
                'data' => '',
            ]);
        }

        return $quantity;
    }

    public function take(int $itemId, int $quantity, PermissionEntityType $ownerType, int $ownerId, int $fromWear, ?int $toWear = null): int
    {
        $remaining = max(0, $quantity);
        $taken = 0;

        foreach ($this->inventories($itemId, $ownerType, $ownerId, $fromWear, lock: true)->get() as $inventory) {
            if ($remaining <= 0) {
                break;
            }

            if ($inventory->item?->stackable) {
                $amount = min($remaining, (int) $inventory->stack);
                $remaining -= $amount;
                $taken += $amount;

                if ($toWear !== null) {
                    $this->createOrIncrement($itemId, $amount, $ownerType, $ownerId, $toWear, $inventory);
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
    public function moveInventory(Inventory $inventory, PermissionEntityType $toOwnerType, int $toOwnerId, int $toWear, int|string|null $requestedStack = null): array
    {
        $inventory->loadMissing('item');
        $item = $inventory->item;

        if (! $item) {
            return [(int) $inventory->item_id, 0];
        }

        if (! $item->stackable) {
            $inventory->update([
                'owner_type' => $toOwnerType->value,
                'owner_id' => $toOwnerId,
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

        $target = $this->inventory($item->id, $toOwnerType, $toOwnerId, $toWear);

        if ($stack === (int) $inventory->stack && ! $target) {
            $inventory->update([
                'owner_type' => $toOwnerType->value,
                'owner_id' => $toOwnerId,
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
                    'item_id' => $item->id,
                    'stack' => $stack,
                    'wear' => $toWear,
                    'owner_type' => $toOwnerType->value,
                    'owner_id' => $toOwnerId,
                    'timelastvalue' => 0,
                    'data' => '',
                ]);
            }

            $inventory->decrement('stack', $stack);
        }

        return [$item->id, $stack];
    }

    private function createOrIncrement(int $itemId, int $amount, PermissionEntityType $ownerType, int $ownerId, int $wear, Inventory $source): void
    {
        $target = $this->inventory($itemId, $ownerType, $ownerId, $wear, lock: true);

        if ($target) {
            $target->increment('stack', $amount);

            return;
        }

        Inventory::create([
            'item_id' => $itemId,
            'stack' => $amount,
            'wear' => $wear,
            'owner_type' => $ownerType->value,
            'owner_id' => $ownerId,
            'timelastvalue' => $source->timelastvalue ?? 0,
            'data' => $source->data ?? '',
        ]);
    }

    private function inventory(int $itemId, PermissionEntityType $ownerType, int $ownerId, int $wear, bool $lock = false): ?Inventory
    {
        return $this->inventories($itemId, $ownerType, $ownerId, $wear, $lock)->first();
    }

    private function inventories(int $itemId, PermissionEntityType $ownerType, int $ownerId, int $wear, bool $lock = false)
    {
        $query = Inventory::query()
            ->with('item')
            ->where('item_id', $itemId)
            ->where('owner_type', $ownerType->value)
            ->where('owner_id', $ownerId)
            ->where('wear', $wear)
            ->orderBy('id');

        return $lock ? $query->lockForUpdate() : $query;
    }
}
