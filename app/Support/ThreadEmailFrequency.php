<?php

namespace App\Support;

enum ThreadEmailFrequency: string
{
    case NONE = 'none';
    case IMMEDIATE = 'immediate';
    case ONCE_UNTIL_READ = 'once_until_read';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'Keine E-Mails',
            self::IMMEDIATE => 'Bei jedem neuen Beitrag',
            self::ONCE_UNTIL_READ => 'Einmal, bis ich das Thema gelesen habe',
            self::DAILY => 'Tägliche Zusammenfassung',
            self::WEEKLY => 'Wöchentliche Zusammenfassung',
        };
    }
}
