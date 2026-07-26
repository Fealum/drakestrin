<?php

namespace App\Data\Economy;

use App\Support\PostTransferAction;

class PostTransferData
{
    /**
     * @param  list<TransferInventoryItem>  $items
     */
    public function __construct(
        public readonly PostTransferAction $action,
        public readonly array $items,
        public readonly ?int $recipientCharacterId = null,
        public readonly ?int $companyId = null,
    ) {}

    public static function fromArray(array $data): ?self
    {
        $action = PostTransferAction::tryFrom((string) ($data['transfer_action'] ?? ''));
        $inventoryIds = collect($data['inventory'] ?? [])
            ->map(fn ($inventoryId) => (int) $inventoryId)
            ->filter()
            ->unique()
            ->values();

        if (! $action || $inventoryIds->isEmpty()) {
            return null;
        }

        return new self(
            action: $action,
            items: $inventoryIds
                ->map(fn (int $inventoryId) => new TransferInventoryItem(
                    inventoryId: $inventoryId,
                    requestedStack: $data['inventorystack'][$inventoryId] ?? null,
                ))
                ->all(),
            recipientCharacterId: filled($data['recipient'] ?? null) ? (int) $data['recipient'] : null,
            companyId: filled($data['company'] ?? null) ? (int) $data['company'] : null,
        );
    }
}
