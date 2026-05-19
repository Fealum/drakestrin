<?php

namespace App\Data\Board;

class CreateThreadData
{
    public function __construct(
        public readonly int $boardId,
        public readonly int $characterId,
        public readonly string $name,
        public readonly string $message,
        public readonly bool $important,
        public readonly bool $smilies,
        public readonly bool $signature,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            boardId: (int) $data['board'],
            characterId: (int) $data['character'],
            name: trim($data['name']),
            message: trim($data['message']),
            important: (bool) ($data['important'] ?? false),
            smilies: (bool) ($data['smilies'] ?? false),
            signature: (bool) ($data['signature'] ?? false),
        );
    }
}
