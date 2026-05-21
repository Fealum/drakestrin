<?php

namespace App\Data\Economy;

use App\Support\PermissionEntityType;

class TransferParticipant
{
    public function __construct(
        public readonly ?PermissionEntityType $type,
        public readonly ?int $id,
    ) {
    }

    public static function character(int $id): self
    {
        return new self(PermissionEntityType::CHARACTER, $id);
    }
}
