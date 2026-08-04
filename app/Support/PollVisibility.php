<?php

namespace App\Support;

enum PollVisibility: string
{
    case ANONYMOUS = 'anonymous';
    case OPEN = 'open';
}
