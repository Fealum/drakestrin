<?php

namespace App\Data\Economy;

class InventoryStateChange
{
    public function __construct(
        public readonly ?int $wear = null,
        public readonly ?int $timeLastValue = null,
        public readonly ?string $data = null,
    ) {}

    /** @return array<string, int|string> */
    public function toAttributes(): array
    {
        return array_filter([
            'wear' => $this->wear,
            'timelastvalue' => $this->timeLastValue,
            'data' => $this->data,
        ], fn ($value) => $value !== null);
    }
}
