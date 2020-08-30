<?php

namespace Unit\Models\Domain\Currency;

use App\Exceptions\CurrencyException;
use App\Models\Domain\Currency\CurrencyConverter;
use App\Models\Value\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    private $converter;

    protected function setUp(): void
    {
        $this->converter = new CurrencyConverter([
            $this->getCurrencyMock(Currency::EUR, 1),
            $this->getCurrencyMock(Currency::GBP, 0.891684),
            $this->getCurrencyMock(Currency::USD, 1.19057),
        ]);
    }

    public function testCanConvertCorrectly()
    {
        $amount = $this->converter->convert(15, Currency::EUR, Currency::EUR);
        $this->assertEquals(15, $amount);

        $amount = $this->converter->convert(15, Currency::USD, Currency::USD);
        $this->assertEquals(15, $amount);

        $amount = $this->converter->convert(15, Currency::EUR, Currency::USD);
        $this->assertEquals(17.86, $amount);

        $amount = $this->converter->convert(13.38, Currency::GBP, Currency::USD);
        $this->assertEquals(17.86, $amount);
    }

    public function testExceptionIsThrownIfConvertingFromAnUnsupportedCurrency()
    {
        $this->expectException(CurrencyException::class);
        $this->expectExceptionCode(CurrencyException::UNSUPPORTED_CURRENCY);

        $this->converter->convert(10, 'BGN', Currency::USD);
    }

    public function testExceptionIsThrownIfConvertingToAnUnsupportedCurrency()
    {
        $this->expectException(CurrencyException::class);
        $this->expectExceptionCode(CurrencyException::UNSUPPORTED_CURRENCY);

        $this->converter->convert(10, Currency::USD, 'BGN');
    }

    private function getCurrencyMock(string $currency, float $amount): Currency
    {
        $mock = $this->createMock(Currency::class);
        $mock->method('getCode')
            ->willReturn($currency);
        $mock->method('getRate')
            ->willReturn($amount);

        return $mock;
    }
}
