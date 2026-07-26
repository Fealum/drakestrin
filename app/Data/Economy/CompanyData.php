<?php

namespace App\Data\Economy;

class CompanyData
{
    public function __construct(
        public readonly string $name,
        public readonly int $sector,
        public readonly int $ownerCharacterId,
        public readonly int $locationId,
        public readonly string $description,
        public readonly string $text,
        public readonly string $url,
        public readonly bool $isStorefront,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: trim((string) $data['name']),
            sector: (int) $data['sector'],
            ownerCharacterId: (int) $data['owner_character_id'],
            locationId: (int) $data['location_id'],
            description: trim((string) ($data['description'] ?? '')),
            text: trim((string) ($data['text'] ?? '')),
            url: trim((string) ($data['url'] ?? '')),
            isStorefront: (bool) ($data['is_storefront'] ?? false),
        );
    }
}
