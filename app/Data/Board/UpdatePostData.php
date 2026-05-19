<?php

namespace App\Data\Board;

class UpdatePostData
{
    public function __construct(
        public readonly int $characterId,
        public readonly string $message,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            characterId: (int) $data['character'],
            message: trim($data['message']),
        );
    }
}
