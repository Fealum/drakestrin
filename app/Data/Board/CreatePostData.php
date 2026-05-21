<?php

namespace App\Data\Board;

use App\Data\Economy\TransferInventoryItem;

class CreatePostData
{
    /**
     * @param list<TransferInventoryItem> $transferItems
     */
    public function __construct(
        public readonly string $character,
        public readonly string $message,
        public readonly ?string $newCharacterName,
        public readonly bool $smilies,
        public readonly bool $signature,
        public readonly ?int $transferRecipientId = null,
        public readonly array $transferItems = [],
    ) {
    }

    public function hasTransfer(): bool
    {
        return $this->transferItems !== [];
    }

    public static function fromArray(array $data): self
    {
        $inventoryIds = collect($data['inventory'] ?? [])
            ->map(fn ($inventoryId) => (int) $inventoryId)
            ->filter()
            ->unique()
            ->values();

        return new self(
            character: (string) $data['character'],
            message: trim((string) ($data['message'] ?? '')),
            newCharacterName: isset($data['newcharname']) ? trim((string) $data['newcharname']) : null,
            smilies: (bool) ($data['smilies'] ?? false),
            signature: (bool) ($data['signature'] ?? false),
            transferRecipientId: filled($data['recipient'] ?? null) ? (int) $data['recipient'] : null,
            transferItems: $inventoryIds
                ->map(fn (int $inventoryId) => new TransferInventoryItem(
                    inventoryId: $inventoryId,
                    requestedStack: $data['inventorystack'][$inventoryId] ?? null,
                ))
                ->all(),
        );
    }
}
