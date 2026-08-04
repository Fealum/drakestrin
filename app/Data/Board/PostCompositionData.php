<?php

namespace App\Data\Board;

use App\Support\PostElementType;
use InvalidArgumentException;

class PostCompositionData
{
    /** @param list<PostElementData> $elements */
    public function __construct(public readonly array $elements) {}

    public static function fromArray(array $rows): self
    {
        $elements = collect($rows)->values()->map(function (array $row) {
            $type = PostElementType::tryFrom((string) ($row['type'] ?? ''));

            return match ($type) {
                PostElementType::MESSAGE => MessageElementData::fromArray($row),
                PostElementType::TRANSFER => TransferElementData::fromArray($row)
                    ?? throw new InvalidArgumentException('Eine Handlung benötigt eine Art und mindestens einen Gegenstand.'),
                PostElementType::SCENE_TRANSITION => SceneTransitionElementData::fromArray($row),
                PostElementType::POLL => PollElementData::fromArray($row),
                default => throw new InvalidArgumentException('Unknown post element type.'),
            };
        })->values()->all();

        return new self($elements);
    }

    public function toArray(): array
    {
        return array_map(fn (PostElementData $element) => $element->toArray(), $this->elements);
    }
}
