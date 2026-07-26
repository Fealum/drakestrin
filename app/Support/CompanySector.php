<?php

namespace App\Support;

enum CompanySector: int
{
    case MINING = 1;
    case AGRICULTURE = 2;
    case CRAFT = 3;
    case SERVICES = 4;

    public function label(): string
    {
        return match ($this) {
            self::MINING => 'Bergbau',
            self::AGRICULTURE => 'Landwirtschaft',
            self::CRAFT => 'Handwerk',
            self::SERVICES => 'Dienstleistungen und Handel',
        };
    }
}
