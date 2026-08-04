<?php

namespace App\Services\Economy;

use App\Data\Economy\InventoryMutationContext;
use App\Data\Economy\InventoryOwner;
use App\Data\Economy\TransferContext;
use App\Data\Economy\TransferInventoryItem;
use App\Data\Economy\TransferParticipant;
use App\Models\Economy\Inventory;
use App\Models\Economy\Transfer;
use App\Models\Economy\TransferItem;
use App\Services\InventoryService;
use App\Support\InventoryMutationClock;
use App\Support\InventoryMutationKind;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransferService
{
    public function __construct(
        private InventoryService $inventory,
        private InventoryTimeline $timeline,
    ) {}

    /**
     * @param  list<TransferInventoryItem>  $items
     */
    public function transferInventories(
        ?int $postId,
        ?TransferParticipant $sender,
        ?TransferParticipant $recipient,
        InventoryOwner $source,
        InventoryOwner $target,
        array $items,
        ?int $postElementId = null,
        ?TransferContext $context = null,
        ?int $reversalOfTransferId = null,
    ): Transfer {
        return DB::transaction(function () use ($postId, $postElementId, $sender, $recipient, $source, $target, $items, $context, $reversalOfTransferId) {
            $itemsByInventoryId = collect($items)
                ->keyBy(fn (TransferInventoryItem $item) => $item->inventoryId);

            if ($itemsByInventoryId->isEmpty()) {
                throw new InvalidArgumentException('No inventory items selected.');
            }

            if (($source->type === PermissionEntityType::LOCATION || $target->type === PermissionEntityType::LOCATION) && $context === null) {
                throw new InvalidArgumentException('Location transfers require a scene and story time.');
            }

            $this->lockOwners($source, $target);

            $inventories = Inventory::query()
                ->with('item')
                ->whereIn('id', $itemsByInventoryId->keys())
                ->ownedBy($source->type, $source->id)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($inventories->count() !== $itemsByInventoryId->count() || $inventories->contains(fn (Inventory $inventory) => ! $inventory->item)) {
                throw new InvalidArgumentException('No transferable inventory items found.');
            }

            if ($context !== null) {
                $quantities = $itemsByInventoryId
                    ->map(function (TransferInventoryItem $selectedItem, int $inventoryId) use ($inventories) {
                        $inventory = $inventories->get($inventoryId);

                        if (! $inventory->item->stackable) {
                            return null;
                        }

                        $quantity = $inventory->undounitary($selectedItem->requestedStack ?? 1);
                        $quantity = min((int) $inventory->stack, max(1, $quantity));

                        return ['item_id' => $inventory->item_id, 'quantity' => $quantity];
                    })
                    ->filter()
                    ->groupBy('item_id')
                    ->map(fn ($rows) => $rows->sum('quantity'))
                    ->all();

                $this->timeline->assertRemovable($source, $context->storyAt, $quantities);

                $inventories
                    ->filter(fn (Inventory $inventory) => ! $inventory->item->stackable)
                    ->each(fn (Inventory $inventory) => $this->timeline->assertInstanceRemovable(
                        $source,
                        $inventory->id,
                        $context->storyAt,
                    ));
            }

            $transfer = Transfer::create([
                'reversal_of_transfer_id' => $reversalOfTransferId,
                'post_id' => $postId,
                'post_element_id' => $postElementId,
                'thread_scene_id' => $context?->threadSceneId,
                'story_at' => $context?->storyAt,
                'created_by_user_id' => $context?->createdByUserId,
                'acted_by_character_id' => $context?->actedByCharacterId,
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
                    $selectedItem->targetWear ?? $target->wear,
                    $selectedItem->requestedStack,
                    new InventoryMutationContext(
                        InventoryMutationKind::TRANSFER,
                        $context ? InventoryMutationClock::STORY : InventoryMutationClock::ADMIN,
                        $context?->storyAt ?? now()->timestamp,
                        'transfer',
                        $transfer->id,
                    ),
                );
                $isInstance = ! $inventory->item->stackable;

                if ($isInstance) {
                    $inventory->refresh();
                }

                TransferItem::create([
                    'transfer_id' => $transfer->id,
                    'item_id' => $itemId,
                    'inventory_id' => $isInstance ? $inventory->id : null,
                    'inventory_state' => $isInstance ? $this->instanceState($inventory) : null,
                    'stack' => $stack,
                ]);
            }

            return $transfer;
        });
    }

    /**
     * @return array{item_id:int,wear:int,timelastvalue:int,data:string}
     */
    private function instanceState(Inventory $inventory): array
    {
        return [
            'item_id' => (int) $inventory->item_id,
            'wear' => (int) $inventory->wear,
            'timelastvalue' => (int) $inventory->timelastvalue,
            'data' => (string) $inventory->data,
        ];
    }

    private function lockOwners(InventoryOwner ...$owners): void
    {
        collect($owners)
            ->unique(fn (InventoryOwner $owner) => $owner->type->value.':'.$owner->id)
            ->sortBy(fn (InventoryOwner $owner) => [$owner->type->value, $owner->id])
            ->each(function (InventoryOwner $owner) {
                $modelClass = $owner->type->modelClass();
                $modelClass::query()->whereKey($owner->id)->lockForUpdate()->firstOrFail();
            });
    }
}
