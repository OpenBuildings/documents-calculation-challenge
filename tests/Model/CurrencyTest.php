<?php


namespace App\Test\Model;

use App\Model\Currency;
use PHPUnit\Framework\TestCase;

/**
 * Class CurrencyTest
 * @package App\Test\Model
 */
class CurrencyTest extends TestCase
{
    public function testInit()
    {
        $currency = new Currency('GBP', 2.18);

        $this->assertSame('GBP', $currency->getName());
        $this->assertSame(2.18, $currency->getRate());
    }
}
