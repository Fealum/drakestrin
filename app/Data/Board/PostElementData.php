<?php

namespace App\Data\Board;

use App\Support\PostElementType;

interface PostElementData
{
    public function type(): PostElementType;

    public function toArray(): array;
}
