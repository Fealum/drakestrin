<?php

namespace App\Support;

class Currency
{
    public const TEN_PER_TUK = 8_192;

    public const TUK_PER_TIL = 1_024;

    public const TEN_PER_TIL = self::TEN_PER_TUK * self::TUK_PER_TIL;

    /**
     * @return array{til:int,tuk:int,ten:int}
     */
    public static function denominations(int $ten): array
    {
        $ten = max(0, $ten);
        $til = intdiv($ten, self::TEN_PER_TIL);
        $ten %= self::TEN_PER_TIL;
        $tuk = intdiv($ten, self::TEN_PER_TUK);

        return [
            'til' => $til,
            'tuk' => $tuk,
            'ten' => $ten % self::TEN_PER_TUK,
        ];
    }

    public static function toTen(int $til, int $tuk, int $ten): int
    {
        return ($til * self::TEN_PER_TIL) + ($tuk * self::TEN_PER_TUK) + $ten;
    }

    public static function format(int $ten): string
    {
        $parts = self::denominations($ten);
        $formatted = collect([
            'tl' => $parts['til'],
            'tk' => $parts['tuk'],
            'tn' => $parts['ten'],
        ])->filter(fn (int $amount) => $amount > 0)
            ->map(fn (int $amount, string $unit) => $amount.' '.$unit)
            ->implode(' ');

        return $formatted !== '' ? $formatted : '0 tn';
    }
}
