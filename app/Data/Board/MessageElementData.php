<?php

namespace App\Data\Board;

use App\Support\PostElementType;

class MessageElementData implements PostElementData
{
    public function __construct(public readonly string $message, public readonly bool $smilies = true) {}

    public function type(): PostElementType
    {
        return PostElementType::MESSAGE;
    }

    public function toArray(): array
    {
        return ['type' => $this->type()->value, 'message' => $this->message, 'smilies' => $this->smilies];
    }

    public static function fromArray(array $data): self
    {
        return new self(trim((string) ($data['message'] ?? '')), (bool) ($data['smilies'] ?? false));
    }
}
