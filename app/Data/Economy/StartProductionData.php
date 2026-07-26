<?php

namespace App\Data\Economy;

class StartProductionData
{
    public function __construct(
        public readonly int $quantity,
        public readonly int $instances,
        public readonly int $outputState,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            quantity: (int) $data['quantity'] === 0 ? (int) ($data['quantity_count'] ?? 1) : -1,
            instances: max(1, (int) $data['instances']),
            outputState: (int) $data['prodas'] === 0 ? (int) ($data['prodas_value'] ?? 0) : (int) $data['prodas'],
        );
    }
}
