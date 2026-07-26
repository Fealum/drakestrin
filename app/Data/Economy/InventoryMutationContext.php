<?php

namespace App\Data\Economy;

use App\Support\InventoryMutationClock;
use App\Support\InventoryMutationKind;

class InventoryMutationContext
{
    public function __construct(
        public readonly InventoryMutationKind $kind,
        public readonly InventoryMutationClock $clock,
        public readonly int $effectiveAt,
        public readonly ?string $sourceType = null,
        public readonly ?int $sourceId = null,
    ) {}

    public static function adjustment(?int $effectiveAt = null): self
    {
        return new self(
            InventoryMutationKind::ADJUSTMENT,
            InventoryMutationClock::ADMIN,
            $effectiveAt ?? now()->timestamp,
        );
    }

    public static function stateChange(?int $effectiveAt = null): self
    {
        return new self(
            InventoryMutationKind::STATE_CHANGE,
            InventoryMutationClock::ADMIN,
            $effectiveAt ?? now()->timestamp,
        );
    }
}
