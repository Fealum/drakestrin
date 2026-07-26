<?php

namespace App\Data\Board;

class UpdateThreadData
{
    public function __construct(
        public readonly int $boardId,
        public readonly string $name,
        public readonly bool $important,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            boardId: (int) $data['board'],
            name: trim($data['name']),
            important: (bool) ($data['important'] ?? false),
        );
    }
}
