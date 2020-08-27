<?php

use App\Currency;

class CurrencyTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        Currency::$supportedCurrencies = ['USD', 'EUR', 'GBP'];

        $instance = new Currency('EUR', 1);
        $this->assertInstanceOf(Currency::class, $instance);
    }

    public function testConstructException()
    {
        Currency::$supportedCurrencies = ['USD', 'GBP'];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported currency: EUR');

        $instance = new Currency('EUR', 1);
        $this->assertInstanceOf(Currency::class, $instance);
    }

    public function testGetRate()
    {
        Currency::$supportedCurrencies = ['USD', 'EUR', 'GBP'];

        $instance = new Currency('EUR', 1);
        $this->assertEquals(1.0, $instance->getRate());
    }
}
