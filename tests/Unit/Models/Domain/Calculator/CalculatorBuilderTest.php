<?php

namespace Unit\Models\Domain\Calculator;

use App\Models\Domain\Calculator\Calculator;
use App\Models\Domain\Calculator\CalculatorBuilder;
use App\Models\Value\Column;
use App\Models\Value\Currency;
use PHPUnit\Framework\TestCase;

class CalculatorBuilderTest extends TestCase
{
    public function testCanBuildCalculator()
    {
        $calculator = (new CalculatorBuilder())
            ->setData([])
            ->setCurrencies([
                $this->createMock(Currency::class),
            ])
            ->setOutputCurrency($this->createMock(Currency::class))
            ->build();

        $this->assertInstanceOf(Calculator::class, $calculator);
    }

    public function testCanBuildCalculatorWithFilters()
    {
        $calculator = (new CalculatorBuilder())
            ->setData([])
            ->setCurrencies([
                $this->createMock(Currency::class),
            ])
            ->setFilters([
                $this->createMock(Column::class),
                $this->createMock(Column::class),
            ])
            ->setOutputCurrency($this->createMock(Currency::class))
            ->build();

        $this->assertInstanceOf(Calculator::class, $calculator);
    }
}
