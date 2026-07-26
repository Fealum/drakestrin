<?php

namespace App\Support;

enum InventoryMutationClock: string
{
    case ADMIN = 'admin';
    case SIMULATION = 'simulation';
    case STORY = 'story';
}
