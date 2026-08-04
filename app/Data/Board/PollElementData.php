<?php

namespace App\Data\Board;

use App\Support\PollVisibility;
use App\Support\PostElementType;

class PollElementData implements PostElementData
{
    public function __construct(
        public readonly string $question,
        public readonly array $options,
        public readonly PollVisibility $visibility,
        public readonly int $maxChoices = 1,
        public readonly ?int $closesAt = null,
    ) {}

    public function type(): PostElementType
    {
        return PostElementType::POLL;
    }

    public function toArray(): array
    {
        return ['type' => $this->type()->value, 'question' => $this->question, 'options' => $this->options, 'visibility' => $this->visibility->value, 'max_choices' => $this->maxChoices, 'closes_at' => $this->closesAt];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            trim((string) ($data['question'] ?? '')),
            collect($data['options'] ?? [])->map(fn ($option) => trim((string) $option))->filter()->values()->all(),
            PollVisibility::tryFrom((string) ($data['visibility'] ?? '')) ?? PollVisibility::ANONYMOUS,
            max(1, (int) ($data['max_choices'] ?? 1)),
            filled($data['closes_at'] ?? null) ? (int) $data['closes_at'] : null,
        );
    }
}
