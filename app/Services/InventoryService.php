<?php

namespace App\Services;

use App\Data\Economy\InventoryMutationContext;
use App\Data\Economy\InventoryStateChange;
use App\Models\Economy\Inventory;
use App\Models\Economy\Item;
use App\Services\Economy\InventoryMutationRecorder;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(private InventoryMutationRecorder $mutations) {}

    public function available(int $itemId, PermissionEntityType $ownerType, int $ownerId, int $wear): int
    {
        return $this->inventories($itemId, $ownerType, $ownerId, $wear)
            ->get()
            ->sum(fn (Inventory $inventory) => $inventory->item?->stackable ? (int) $inventory->stack : 1);
    }

    public function debitStack(
        int $itemId,
        int $amount,
        PermissionEntityType $ownerType,
        int $ownerId,
        int $wear,
        ?InventoryMutationContext $context = null,
    ): int {
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
            $before = $this->mutations->snapshot($inventory);

            if ($take === $available) {
                $inventory->delete();
                $this->mutations->record($inventory, $before, null, $context);

                continue;
            }

            $inventory->decrement('stack', $take);
            $inventory->refresh();
            $this->mutations->record($inventory, $before, $this->mutations->snapshot($inventory), $context);
        }

        return $paid;
    }

    public function add(
        int $itemId,
        int $quantity,
        PermissionEntityType $ownerType,
        int $ownerId,
        int $wear,
        ?InventoryMutationContext $context = null,
    ): int {
        $quantity = max(0, $quantity);
        $item = Item::find($itemId);

        if (! $item || $quantity === 0) {
            return 0;
        }

        if ($item->stackable) {
            $target = $this->inventory($itemId, $ownerType, $ownerId, $wear, lock: true);

            if ($target) {
                $before = $this->mutations->snapshot($target);
                $target->increment('stack', $quantity);
                $target->refresh();
                $this->mutations->record($target, $before, $this->mutations->snapshot($target), $context);
            } else {
                $target = Inventory::create([
                    'item_id' => $itemId,
                    'stack' => $quantity,
                    'wear' => $wear,
                    'owner_type' => $ownerType->value,
                    'owner_id' => $ownerId,
                    'timelastvalue' => 0,
                    'data' => '',
                ]);
                $this->mutations->record($target, null, $this->mutations->snapshot($target), $context);
            }

            return $quantity;
        }

        for ($i = 0; $i < $quantity; $i++) {
            $inventory = Inventory::create([
                'item_id' => $itemId,
                'stack' => 0,
                'wear' => $wear,
                'owner_type' => $ownerType->value,
                'owner_id' => $ownerId,
                'timelastvalue' => 0,
                'data' => '',
            ]);
            $this->mutations->record($inventory, null, $this->mutations->snapshot($inventory), $context);
        }

        return $quantity;
    }

    public function take(
        int $itemId,
        int $quantity,
        PermissionEntityType $ownerType,
        int $ownerId,
        int $fromWear,
        ?int $toWear = null,
        ?InventoryMutationContext $context = null,
    ): int {
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
                $before = $this->mutations->snapshot($inventory);

                if ($toWear !== null) {
                    $this->createOrIncrement($itemId, $amount, $ownerType, $ownerId, $toWear, $inventory, $context);
                }

                if ((int) $inventory->stack === $amount) {
                    $inventory->delete();
                    $this->mutations->record($inventory, $before, null, $context);
                } else {
                    $inventory->decrement('stack', $amount);
                    $inventory->refresh();
                    $this->mutations->record($inventory, $before, $this->mutations->snapshot($inventory), $context);
                }

                continue;
            }

            $remaining--;
            $taken++;
            $before = $this->mutations->snapshot($inventory);

            if ($toWear !== null) {
                $inventory->update(['wear' => $toWear]);
                $inventory->refresh();
                $this->mutations->record($inventory, $before, $this->mutations->snapshot($inventory), $context);
            } else {
                $inventory->delete();
                $this->mutations->record($inventory, $before, null, $context);
            }
        }

        return $taken;
    }

    /**
     * @return array{0:int,1:int}
     */
    public function moveInventory(
        Inventory $inventory,
        PermissionEntityType $toOwnerType,
        int $toOwnerId,
        int $toWear,
        int|string|null $requestedStack = null,
        ?InventoryMutationContext $context = null,
    ): array {
        $inventory->loadMissing('item');
        $item = $inventory->item;

        if (! $item) {
            return [(int) $inventory->item_id, 0];
        }

        if (! $item->stackable) {
            $before = $this->mutations->snapshot($inventory);
            $inventory->update([
                'owner_type' => $toOwnerType->value,
                'owner_id' => $toOwnerId,
                'wear' => $toWear,
            ]);
            $inventory->refresh();
            $this->mutations->record($inventory, $before, $this->mutations->snapshot($inventory), $context);

            return [$item->id, 0];
        }

        $stack = $inventory->undounitary($requestedStack ?? 1);

        if ($stack <= 0) {
            $stack = 1;
        } elseif ($stack > $inventory->stack) {
            $stack = $inventory->stack;
        }

        $target = $this->inventory($item->id, $toOwnerType, $toOwnerId, $toWear);
        $before = $this->mutations->snapshot($inventory);

        if ($stack === (int) $inventory->stack && ! $target) {
            $inventory->update([
                'owner_type' => $toOwnerType->value,
                'owner_id' => $toOwnerId,
                'wear' => $toWear,
            ]);
            $inventory->refresh();
            $this->mutations->record($inventory, $before, $this->mutations->snapshot($inventory), $context);
        } elseif ($stack === (int) $inventory->stack && $target) {
            $targetBefore = $this->mutations->snapshot($target);
            $target->increment('stack', $stack);
            $target->refresh();
            $this->mutations->record($target, $targetBefore, $this->mutations->snapshot($target), $context);
            $inventory->delete();
            $this->mutations->record($inventory, $before, null, $context);
        } else {
            if ($target) {
                $targetBefore = $this->mutations->snapshot($target);
                $target->increment('stack', $stack);
                $target->refresh();
                $this->mutations->record($target, $targetBefore, $this->mutations->snapshot($target), $context);
            } else {
                $target = Inventory::create([
                    'item_id' => $item->id,
                    'stack' => $stack,
                    'wear' => $toWear,
                    'owner_type' => $toOwnerType->value,
                    'owner_id' => $toOwnerId,
                    'timelastvalue' => 0,
                    'data' => '',
                ]);
                $this->mutations->record($target, null, $this->mutations->snapshot($target), $context);
            }

            $inventory->decrement('stack', $stack);
            $inventory->refresh();
            $this->mutations->record($inventory, $before, $this->mutations->snapshot($inventory), $context);
        }

        return [$item->id, $stack];
    }

    public function updateState(
        Inventory $inventory,
        InventoryStateChange $change,
        ?InventoryMutationContext $context = null,
    ): Inventory {
        return DB::transaction(function () use ($inventory, $change, $context) {
            $inventory = Inventory::query()->whereKey($inventory->id)->lockForUpdate()->firstOrFail();
            $attributes = $change->toAttributes();

            if ($attributes === []) {
                return $inventory;
            }

            $before = $this->mutations->snapshot($inventory);
            $inventory->fill($attributes);

            if (! $inventory->isDirty()) {
                return $inventory;
            }

            $inventory->save();
            $inventory->refresh();
            $this->mutations->record(
                $inventory,
                $before,
                $this->mutations->snapshot($inventory),
                $context ?? InventoryMutationContext::stateChange(),
            );

            return $inventory;
        });
    }

    private function createOrIncrement(
        int $itemId,
        int $amount,
        PermissionEntityType $ownerType,
        int $ownerId,
        int $wear,
        Inventory $source,
        ?InventoryMutationContext $context,
    ): void {
        $target = $this->inventory($itemId, $ownerType, $ownerId, $wear, lock: true);

        if ($target) {
            $before = $this->mutations->snapshot($target);
            $target->increment('stack', $amount);
            $target->refresh();
            $this->mutations->record($target, $before, $this->mutations->snapshot($target), $context);

            return;
        }

        $target = Inventory::create([
            'item_id' => $itemId,
            'stack' => $amount,
            'wear' => $wear,
            'owner_type' => $ownerType->value,
            'owner_id' => $ownerId,
            'timelastvalue' => $source->timelastvalue ?? 0,
            'data' => $source->data ?? '',
        ]);
        $this->mutations->record($target, null, $this->mutations->snapshot($target), $context);
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
