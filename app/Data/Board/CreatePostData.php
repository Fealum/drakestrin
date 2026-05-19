<?php

namespace App\Data\Board;

class CreatePostData
{
    public function __construct(
        public readonly string $character,
        public readonly string $message,
        public readonly ?string $newCharacterName,
        public readonly bool $smilies,
        public readonly bool $signature,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            character: (string) $data['character'],
            message: trim($data['message']),
            newCharacterName: isset($data['newcharname']) ? trim((string) $data['newcharname']) : null,
            smilies: (bool) ($data['smilies'] ?? false),
            signature: (bool) ($data['signature'] ?? false),
        );
    }
}
