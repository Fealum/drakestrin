<?php

namespace App\Data\Economy;

final readonly class CompanyInventoryChange
{
    public function __construct(
        public int $inventoryId,
        public int $targetWear,
        public int|string|null $requestedQuantity,
    ) {}
}
