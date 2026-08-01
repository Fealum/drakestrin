<?php

namespace App\Support;

enum CompanyRepresentativeRole: string
{
    case MANAGER = 'manager';
    case FOREMAN = 'foreman';
    case CLERK = 'clerk';

    public function label(): string
    {
        return match ($this) {
            self::MANAGER => 'Geschäftsführung',
            self::FOREMAN => 'Vorarbeiter',
            self::CLERK => 'Verkäufer',
        };
    }

    public function isSiteSpecific(): bool
    {
        return $this !== self::MANAGER;
    }
}
