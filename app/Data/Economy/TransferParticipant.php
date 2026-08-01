<?php

namespace App\Data\Economy;

use App\Support\PermissionEntityType;

class TransferParticipant
{
    public function __construct(
        public readonly ?PermissionEntityType $type,
        public readonly ?int $id,
    ) {}

    public static function character(int $id): self
    {
        return new self(PermissionEntityType::CHARACTER, $id);
    }

    public static function location(int $id): self
    {
        return new self(PermissionEntityType::LOCATION, $id);
    }

    public static function companySite(int $id): self
    {
        return new self(PermissionEntityType::COMPANY_SITE, $id);
    }
}
