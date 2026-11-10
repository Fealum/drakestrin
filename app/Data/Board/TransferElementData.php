<?php

namespace App\Data\Board;

use App\Data\Economy\PostTransferData;
use App\Support\PermissionEntityType;
use App\Support\PostElementType;

class TransferElementData implements PostElementData
{
    public function __construct(public readonly PostTransferData $transfer, public readonly ?string $sceneKey = null) {}

    public function type(): PostElementType
    {
        return PostElementType::TRANSFER;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type()->value,
            'transfer_action' => $this->transfer->action?->value,
            'source' => $this->party($this->transfer->sourceType, $this->transfer->sourceId),
            'recipient_party' => $this->party($this->transfer->recipientType, $this->transfer->recipientId),
            'inventory' => collect($this->transfer->items)->mapWithKeys(fn ($item) => [$item->inventoryId => $item->inventoryId])->all(),
            'inventorystack' => collect($this->transfer->items)->mapWithKeys(fn ($item) => [$item->inventoryId => $item->requestedStack])->all(),
            'recipient' => $this->transfer->recipientCharacterId,
            'company_site' => $this->transfer->companySiteId,
            'scene_key' => $this->sceneKey,
        ];
    }

    public static function fromArray(array $data): ?self
    {
        $transfer = PostTransferData::fromArray($data);

        return $transfer ? new self($transfer, filled($data['scene_key'] ?? null) ? (string) $data['scene_key'] : null) : null;
    }

    private function party($type, ?int $id): ?string
    {
        if (! $type) {
            return null;
        }

        $name = match ($type) {
            PermissionEntityType::CHARACTER => 'character',
            PermissionEntityType::COMPANY_SITE => 'company_site',
            PermissionEntityType::LOCATION => 'location',
            default => null,
        };

        return $name ? $name.($id ? ':'.$id : '') : null;
    }
}
