<?php

namespace App\Data\Economy;

class TransferInventoryItem
{
    public function __construct(
        public readonly int $inventoryId,
        public readonly int|string|null $requestedStack = null,
        public readonly ?int $targetWear = null,
    ) {}
}
