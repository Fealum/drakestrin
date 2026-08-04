<?php

namespace App\Support;

enum PostElementType: string
{
    case MESSAGE = 'message';
    case TRANSFER = 'transfer';
    case SCENE_TRANSITION = 'scene_transition';
    case POLL = 'poll';
}
