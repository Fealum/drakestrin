<?php

namespace Tests\Unit;

use App\Models\Economy\Item;
use App\Support\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function test_currency_uses_the_retconned_denominations(): void
    {
        $this->assertSame(8_192, Currency::TEN_PER_TUK);
        $this->assertSame(1_024, Currency::TUK_PER_TIL);
        $this->assertSame(8_388_608, Currency::TEN_PER_TIL);
        $this->assertSame(
            ['til' => 1, 'tuk' => 2, 'ten' => 512],
            Currency::denominations(Currency::toTen(1, 2, 512)),
        );
        $this->assertSame(
            ['til' => 0, 'tuk' => 1, 'ten' => 1],
            Currency::denominations(Currency::toTen(0, 0, 8_193)),
        );
    }

    public function test_formatter_omits_empty_denominations(): void
    {
        $this->assertSame('0 tn', Currency::format(0));
        $this->assertSame('512 tn', Currency::format(512));
        $this->assertSame('2 tk 256 tn', Currency::format(Currency::toTen(0, 2, 256)));
        $this->assertSame('1 tl', Currency::format(Currency::TEN_PER_TIL));
    }

    public function test_money_item_parses_and_formats_the_new_units(): void
    {
        $item = new Item;
        $item->unit = 't';
        $item->weight = 1;
        $ten = Currency::toTen(1, 2, 512);

        $this->assertSame('1tl2tk512tn', $item->makeunitary($ten));
        $this->assertSame($ten, $item->undounitary('1tl2tk512tn'));
    }
}
