<?php

namespace App\Services\Economy;

use App\Data\Economy\InventoryOwner;
use App\Data\Economy\TransferContext;
use App\Data\Economy\TransferInventoryItem;
use App\Data\Economy\TransferParticipant;
use App\Models\Board\Thread as ForumThread;
use App\Models\Economy\Inventory;
use App\Models\Economy\Transfer;
use App\Models\User;
use App\Services\PermissionService;
use App\Support\PermissionEntityType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class TransferReversalService
{
    public const REVERSAL_WINDOW_MINUTES = 5;

    public function __construct(
        private PermissionService $permissions,
        private TransferService $transfers,
    ) {}

    public function canReverse(Transfer $transfer, ?User $user): bool
    {
        if (! $user || $transfer->created_by_user_id !== $user->id || $transfer->reversal_of_transfer_id !== null) {
            return false;
        }

        $transfer->loadMissing(['post.thread', 'reversal']);
        $post = $transfer->post;
        $thread = $post?->thread;

        $eligible = $thread
            && $transfer->created_at?->gte(now()->subMinutes(self::REVERSAL_WINDOW_MINUTES))
            && $transfer->reversal === null
            && $thread->last_post_id === $post->id
            && $transfer->thread_scene_id !== null
            && $transfer->story_at !== null
            && PermissionEntityType::fromDatabase($transfer->sender_type)
            && PermissionEntityType::fromDatabase($transfer->recipient_type)
            && $transfer->sender_id !== null
            && $transfer->recipient_id !== null
            && $this->permissions->allows('transfer', $thread, $user);

        if (! $eligible) {
            return false;
        }

        try {
            $sourceType = PermissionEntityType::fromDatabase($transfer->recipient_type);
            $this->inventoryItemsForReversal(
                $transfer->loadMissing('items.item'),
                new InventoryOwner($sourceType, $transfer->recipient_id),
            );

            return true;
        } catch (ValidationException) {
            return false;
        }
    }

    public function reverse(Transfer $transfer, User $user): Transfer
    {
        return DB::transaction(function () use ($transfer, $user) {
            $transfer = Transfer::query()->whereKey($transfer->id)->lockForUpdate()->firstOrFail();
            $transfer->load(['items.item', 'post']);
            $thread = ForumThread::query()->whereKey($transfer->post?->thread_id)->lockForUpdate()->firstOrFail();
            $transfer->post->setRelation('thread', $thread);
            $transfer->load('reversal');

            abort_unless($this->canReverse($transfer, $user), 403);

            $sourceType = PermissionEntityType::fromDatabase($transfer->recipient_type);
            $targetType = PermissionEntityType::fromDatabase($transfer->sender_type);
            abort_unless($sourceType && $targetType, 422);

            $source = new InventoryOwner($sourceType, $transfer->recipient_id);
            $target = new InventoryOwner($targetType, $transfer->sender_id);

            try {
                return $this->transfers->transferInventories(
                    postId: $transfer->post_id,
                    sender: new TransferParticipant($sourceType, $source->id),
                    recipient: new TransferParticipant($targetType, $target->id),
                    source: $source,
                    target: $target,
                    items: $this->inventoryItemsForReversal($transfer, $source),
                    context: new TransferContext(
                        threadSceneId: $transfer->thread_scene_id,
                        storyAt: $transfer->story_at,
                        createdByUserId: $user->id,
                        actedByCharacterId: $transfer->acted_by_character_id ?? $transfer->post?->character_id,
                    ),
                    reversalOfTransferId: $transfer->id,
                );
            } catch (InvalidArgumentException) {
                throw ValidationException::withMessages([
                    'transfer' => 'Die Handlung kann nicht mehr rückgängig gemacht werden, weil die Gegenstände inzwischen anderweitig benötigt werden.',
                ]);
            }
        });
    }

    /**
     * @return list<TransferInventoryItem>
     */
    private function inventoryItemsForReversal(Transfer $transfer, InventoryOwner $source): array
    {
        $selected = [];

        foreach ($transfer->items->whereNotNull('inventory_id') as $transferItem) {
            $inventory = Inventory::query()
                ->with('item')
                ->whereKey($transferItem->inventory_id)
                ->ownedBy($source->type, $source->id)
                ->first();

            if (! $inventory || $this->instanceState($inventory) !== $transferItem->inventory_state) {
                throw ValidationException::withMessages([
                    'transfer' => 'Der übertragene Gegenstand wurde inzwischen verändert oder befindet sich nicht mehr beim Empfänger.',
                ]);
            }

            $selected[] = new TransferInventoryItem($inventory->id);
        }

        if ($transfer->items->contains(fn ($item) => $item->inventory_id === null && ! $item->item?->stackable)) {
            throw ValidationException::withMessages([
                'transfer' => 'Diese ältere Transaktion enthält keine eindeutige Gegenstandsidentität und kann nicht sicher rückgängig gemacht werden.',
            ]);
        }

        $requiredByItem = $transfer->items
            ->whereNull('inventory_id')
            ->filter(fn ($item) => (bool) $item->item?->stackable)
            ->groupBy('item_id')
            ->map(fn (Collection $items) => $items->sum(fn ($item) => max(1, (int) $item->stack)));
        $inventories = Inventory::query()
            ->with('item')
            ->ownedBy($source->type, $source->id)
            ->whereIn('item_id', $requiredByItem->keys())
            ->orderBy('id')
            ->get()
            ->groupBy('item_id');
        foreach ($requiredByItem as $itemId => $required) {
            foreach ($inventories->get($itemId, collect()) as $inventory) {
                if ($required <= 0) {
                    break;
                }

                if ($inventory->item?->stackable) {
                    $quantity = min($required, (int) $inventory->stack);

                    if ($quantity > 0) {
                        $selected[] = new TransferInventoryItem($inventory->id, $quantity);
                        $required -= $quantity;
                    }
                } else {
                    $selected[] = new TransferInventoryItem($inventory->id);
                    $required--;
                }
            }

            if ($required > 0) {
                throw ValidationException::withMessages([
                    'transfer' => 'Die empfangenen Gegenstände sind nicht mehr vollständig für eine Rückabwicklung verfügbar.',
                ]);
            }
        }

        return $selected;
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
}
