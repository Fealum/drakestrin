<?php

namespace App\Data\Board;

class UpdatePostData
{
    public function __construct(
        public readonly int $characterId,
        public readonly string $message,
        public readonly array $messages = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            characterId: (int) $data['character'],
            message: trim((string) ($data['message'] ?? '')),
            messages: collect($data['messages'] ?? [])->values()->map(fn ($message) => [
                'message' => trim((string) ($message['message'] ?? '')),
                'smilies' => (bool) ($message['smilies'] ?? false),
            ])->all(),
        );
    }
}
