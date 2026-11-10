<?php

namespace App\Data\Economy;

use App\Support\PermissionEntityType;
use App\Support\PostTransferAction;

class PostTransferData
{
    /**
     * @param  list<TransferInventoryItem>  $items
     */
    public function __construct(
        public readonly ?PostTransferAction $action,
        public readonly array $items,
        public readonly ?int $recipientCharacterId = null,
        public readonly ?int $companySiteId = null,
        public readonly ?PermissionEntityType $sourceType = null,
        public readonly ?int $sourceId = null,
        public readonly ?PermissionEntityType $recipientType = null,
        public readonly ?int $recipientId = null,
    ) {}

    public static function fromArray(array $data): ?self
    {
        $action = PostTransferAction::tryFrom((string) ($data['transfer_action'] ?? ''));
        [$sourceType, $sourceId] = self::party($data['source'] ?? null);
        [$recipientType, $recipientId] = self::party($data['recipient_party'] ?? null);
        $inventoryIds = collect($data['inventory'] ?? [])
            ->map(fn ($inventoryId) => (int) $inventoryId)
            ->filter()
            ->unique()
            ->values();

        if ((! $action && (! $sourceType || ! $recipientType)) || $inventoryIds->isEmpty()) {
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
            companySiteId: filled($data['company_site'] ?? null) ? (int) $data['company_site'] : null,
            sourceType: $sourceType,
            sourceId: $sourceId,
            recipientType: $recipientType,
            recipientId: $recipientId,
        );
    }

    private static function party(mixed $value): array
    {
        if (! is_string($value) || $value === '') {
            return [null, null];
        }

        [$type, $id] = array_pad(explode(':', $value, 2), 2, null);
        $entityType = match ($type) {
            'character' => PermissionEntityType::CHARACTER,
            'company_site' => PermissionEntityType::COMPANY_SITE,
            'location' => PermissionEntityType::LOCATION,
            default => null,
        };

        return [$entityType, is_numeric($id) ? (int) $id : null];
    }
}
