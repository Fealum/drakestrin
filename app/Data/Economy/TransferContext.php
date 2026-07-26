<?php

namespace App\Data\Economy;

class TransferContext
{
    public function __construct(
        public readonly int $threadSceneId,
        public readonly int $storyAt,
        public readonly int $createdByUserId,
        public readonly ?int $actedByCharacterId = null,
    ) {}
}
