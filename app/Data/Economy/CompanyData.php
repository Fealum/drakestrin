<?php

namespace App\Data\Economy;

class CompanyData
{
    public function __construct(
        public readonly string $name,
        public readonly int $sector,
        public readonly ?int $ownerCharacterId,
        public readonly ?int $locationId,
        public readonly ?int $newLocationParentTerritoryId,
        public readonly string $description,
    ) {}

    public static function fromArray(array $data): self
    {
        $createsLocation = ($data['location_mode'] ?? null) === 'new';

        return new self(
            name: trim((string) $data['name']),
            sector: (int) $data['sector'],
            ownerCharacterId: filled($data['owner_character_id'] ?? null) ? (int) $data['owner_character_id'] : null,
            locationId: ! $createsLocation && filled($data['location_id'] ?? null) ? (int) $data['location_id'] : null,
            newLocationParentTerritoryId: $createsLocation && filled($data['fauthei_id'] ?? null) ? (int) $data['fauthei_id'] : null,
            description: trim((string) ($data['description'] ?? '')),
        );
    }
}
