<?php

namespace Unit\Models\Value;

use App\Exceptions\CurrencyException;
use App\Models\Value\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function testExpectedDataIsReturned()
    {
        $currency = new Currency(Currency::USD, 1);

        $this->assertEquals(Currency::USD, $currency->getCode());
        $this->assertEquals(1, $currency->getRate());
    }

    public function testExceptionIsThrownIfAnUnsupportedCurrencyIsProvided()
    {
        $this->expectException(CurrencyException::class);
        $this->expectExceptionCode(CurrencyException::UNSUPPORTED_CURRENCY);

        new Currency('RON', 1);
    }
}
