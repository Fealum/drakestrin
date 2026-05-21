<?php

namespace App\Data\Economy;

use App\Support\PermissionEntityType;

class InventoryOwner
{
    public function __construct(
        public readonly PermissionEntityType $type,
        public readonly int $id,
        public readonly int $wear = 0,
    ) {
    }
}
