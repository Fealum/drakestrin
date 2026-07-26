<?php

namespace App\Data\Board;

use App\Data\Economy\PostTransferData;

class CreatePostData
{
    public function __construct(
        public readonly string $character,
        public readonly string $message,
        public readonly ?string $newCharacterName,
        public readonly bool $smilies,
        public readonly bool $signature,
        public readonly ?PostTransferData $transfer = null,
    ) {}

    public function hasTransfer(): bool
    {
        return $this->transfer !== null;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            character: (string) $data['character'],
            message: trim((string) ($data['message'] ?? '')),
            newCharacterName: isset($data['newcharname']) ? trim((string) $data['newcharname']) : null,
            smilies: (bool) ($data['smilies'] ?? false),
            signature: (bool) ($data['signature'] ?? false),
            transfer: PostTransferData::fromArray($data),
        );
    }
}
