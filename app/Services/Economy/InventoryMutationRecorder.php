<?php

namespace App\Services\Economy;

use App\Data\Economy\InventoryMutationContext;
use App\Models\Economy\Inventory;
use App\Models\Economy\InventoryMutation;

class InventoryMutationRecorder
{
    /** @return array{item_id:int,stack:int,wear:int,owner_type:int,owner_id:int,timelastvalue:int,data:string} */
    public function snapshot(Inventory $inventory): array
    {
        return [
            'item_id' => (int) $inventory->item_id,
            'stack' => (int) $inventory->stack,
            'wear' => (int) $inventory->wear,
            'owner_type' => (int) $inventory->owner_type,
            'owner_id' => (int) $inventory->owner_id,
            'timelastvalue' => (int) $inventory->timelastvalue,
            'data' => (string) $inventory->data,
        ];
    }

    public function record(
        Inventory $inventory,
        ?array $beforeState,
        ?array $afterState,
        ?InventoryMutationContext $context = null,
    ): InventoryMutation {
        $context ??= InventoryMutationContext::adjustment();

        return InventoryMutation::create([
            'inventory_id' => $inventory->id,
            'item_id' => $afterState['item_id'] ?? $beforeState['item_id'],
            'kind' => $context->kind,
            'clock' => $context->clock,
            'effective_at' => $context->effectiveAt,
            'source_type' => $context->sourceType,
            'source_id' => $context->sourceId,
            'before_state' => $beforeState,
            'after_state' => $afterState,
        ]);
    }
}
