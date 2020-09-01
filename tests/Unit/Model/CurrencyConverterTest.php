<?php
declare(strict_types=1);

namespace Unit\Model;

use Finance\Model\CurrencyConverter;
use Finance\Model\Exception\ExchangeRateFormatException;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    public function testCanConvertCurrencies()
    {
        $testData = [
            ['EUR:1,USD:2', 10, 'USD', 'EUR', 20],
            ['EUR:1,USD:2,MXN:3.23', 10, 'EUR', 'USD', 5],
            ['BGN:1,EUR:1.583,USD:210,GBP:1.300', 10.355, 'EUR', 'USD', 0.07805697619047619],

        ];

        foreach ($testData as $data) {
            $this->assertEquals(
                (new CurrencyConverter($data[0]))->convert($data[1], $data[2], $data[3]),
                $data[4]
            );
        }
    }

    public function testMissingColon(): void
    {
        $this->expectException(ExchangeRateFormatException::class);
        new CurrencyConverter('EUR:1,USD3');
    }

    public function testInvalidCurrencyLength(): void
    {
        $this->expectException(ExchangeRateFormatException::class);
        new CurrencyConverter('EUR:1,USDUSDUSD:3');
    }

    public function testInvalidExchangeRate(): void
    {
        $this->expectException(ExchangeRateFormatException::class);
        new CurrencyConverter('EUR:1,USD:three');
    }

    public function testMissingBaseCurrency(): void
    {
        $this->expectException(ExchangeRateFormatException::class);
        new CurrencyConverter('EUR:3,USD:2');
    }

    public function testUnsupportedCurrencyConversionFrom(): void
    {
        $this->expectException(ExchangeRateFormatException::class);
        (new CurrencyConverter('EUR:1,USD:2,GBP:3'))
            ->convert(10, 'MXN', 'GBP');
    }

    public function testUnsupportedCurrencyConversionTo(): void
    {
        $this->expectException(ExchangeRateFormatException::class);
        (new CurrencyConverter('EUR:1,USD:2,GBP:3'))
            ->convert(10, 'GBP', 'MXN');
    }
}