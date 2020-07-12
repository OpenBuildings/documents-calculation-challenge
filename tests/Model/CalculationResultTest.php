<?php


namespace App\Test\Model;

use App\Model\CalculationResult;
use PHPUnit\Framework\TestCase;

/**
 * Class CalculationResultTest
 * @package App\Test\Model
 */
class CalculationResultTest extends TestCase
{
    public function testInit()
    {
        $result = new CalculationResult('Customer', 42, 'EUR');

        $this->assertEquals('Customer', $result->getName());
        $this->assertEquals(42, $result->getSum());
        $this->assertEquals('EUR', $result->getCurrency());
    }

    public function testPresent()
    {
        $result = new CalculationResult('Customer 2', 17, 'BGN');

        $presentedResult = $result->present();

        $this->assertEquals('Customer 2 - 17.00 BGN', $presentedResult);
    }
}
