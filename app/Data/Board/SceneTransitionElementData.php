<?php

namespace App\Data\Board;

use App\Support\PostElementType;

class SceneTransitionElementData implements PostElementData
{
    public function __construct(
        public readonly string $action,
        public readonly ?int $locationId = null,
        public readonly ?int $storyAt = null,
        public readonly ?string $sceneKey = null,
    ) {}

    public function type(): PostElementType
    {
        return PostElementType::SCENE_TRANSITION;
    }

    public function toArray(): array
    {
        return ['type' => $this->type()->value, 'scene_action' => $this->action, 'location_id' => $this->locationId, 'story_at' => $this->storyAt, 'scene_key' => $this->sceneKey];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            action: (string) ($data['scene_action'] ?? 'start'),
            locationId: filled($data['location_id'] ?? null) ? (int) $data['location_id'] : null,
            storyAt: filled($data['story_at'] ?? null) ? (int) $data['story_at'] : null,
            sceneKey: filled($data['scene_key'] ?? null) ? (string) $data['scene_key'] : null,
        );
    }
}
