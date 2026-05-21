<?php

namespace App\Services\Economy;

use App\Data\Economy\InventoryOwner;
use App\Data\Economy\TransferInventoryItem;
use App\Data\Economy\TransferParticipant;
use App\Models\Economy\Inventory;
use App\Models\Economy\Transfer;
use App\Models\Economy\TransferItem;
use App\Services\InventoryService;
use InvalidArgumentException;

class TransferService
{
    public function __construct(private InventoryService $inventory)
    {
    }

    /**
     * @param list<TransferInventoryItem> $items
     */
    public function transferInventories(
        ?int $postId,
        ?TransferParticipant $sender,
        ?TransferParticipant $recipient,
        InventoryOwner $source,
        InventoryOwner $target,
        array $items,
    ): Transfer {
        $itemsByInventoryId = collect($items)
            ->keyBy(fn (TransferInventoryItem $item) => $item->inventoryId);

        if ($itemsByInventoryId->isEmpty()) {
            throw new InvalidArgumentException('No inventory items selected.');
        }

        $inventories = Inventory::query()
            ->with('item')
            ->whereIn('id', $itemsByInventoryId->keys())
            ->where('owner_type', $source->type->value)
            ->where('owner_id', $source->id)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($inventories->isEmpty()) {
            throw new InvalidArgumentException('No transferable inventory items found.');
        }

        $transfer = Transfer::create([
            'post_id' => $postId,
            'sender_type' => $sender?->type?->value,
            'sender_id' => $sender?->id,
            'recipient_type' => $recipient?->type?->value,
            'recipient_id' => $recipient?->id,
        ]);

        foreach ($itemsByInventoryId as $inventoryId => $selectedItem) {
            $inventory = $inventories->get($inventoryId);

            if (! $inventory || ! $inventory->item) {
                continue;
            }

            [$itemId, $stack] = $this->inventory->moveInventory(
                $inventory,
                $target->type,
                $target->id,
                $target->wear,
                $selectedItem->requestedStack,
            );

            TransferItem::create([
                'transfer_id' => $transfer->id,
                'item_id' => $itemId,
                'stack' => $stack,
            ]);
        }

        return $transfer;
    }
}
