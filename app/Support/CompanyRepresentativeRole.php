<?php

namespace App\Support;

enum CompanyRepresentativeRole: string
{
    case MANAGER = 'manager';

    public function label(): string
    {
        return match ($this) {
            self::MANAGER => 'Geschäftsführung',
        };
    }
}
