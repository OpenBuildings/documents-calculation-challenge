<?php

namespace Unit\Models\Domain\Calculator;

use App\Exceptions\DocumentException;
use App\Models\Domain\Calculator\Calculator;
use App\Models\Domain\Currency\CurrencyConverter;
use App\Models\Domain\Filter\Filter;
use App\Models\Value\Currency;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testCalculatorOutputIsCorrect()
    {
        $fakeDataFiltered = $this->getFakeData();
        array_pop($fakeDataFiltered);

        $converterMock = $this->createMock(CurrencyConverter::class);
        $converterMock->expects($this->at(0))
            ->method('convert')
            ->with(400, 'USD', 'EUR')
            ->willReturn(335.99);
        $converterMock->expects($this->at(1))
            ->method('convert')
            ->with(100, 'EUR', 'EUR')
            ->willReturn(100.00);
        $converterMock->expects($this->at(2))
            ->method('convert')
            ->with(50, 'GBP', 'EUR')
            ->willReturn(56.07);
        $converterMock->expects($this->at(3))
            ->method('convert')
            ->with(900, 'EUR', 'EUR')
            ->willReturn(900.00);

        $filterMock = $this->createMock(Filter::class);
        $filterMock->method('apply')
            ->with($this->getFakeData())
            ->willReturn($fakeDataFiltered);

        $calculator = new Calculator(
            $this->getFakeData(),
            $this->getCurrencyMock(Currency::EUR),
            $converterMock,
            $filterMock
        );

        $totals = $calculator->getTotals();

        $this->assertEquals([
            'Vendor 1' => 292.06,
            'Vendor 2' => 900,
        ], $totals);
    }

    public function testExceptionIsThrownIfParentIsMissing()
    {
        $this->expectException(DocumentException::class);
        $this->expectExceptionCode(DocumentException::MISSING_PARENT);

        $converterMock = $this->createMock(CurrencyConverter::class);
        $filterMock = $this->createMock(Filter::class);
        $filterMock->method('apply')
            ->with($this->getFakeData())
            ->willReturn([
                [
                    'Customer' => 'Vendor 1',
                    'Document number' => 1000000257,
                    'Type' => 1,
                    'Parent document' => 1000000253,
                    'Currency' => 'USD',
                    'Total' => 400,
                ]
            ]);

        $calculator = new Calculator(
            $this->getFakeData(),
            $this->getCurrencyMock(Currency::EUR),
            $converterMock,
            $filterMock
        );
        $calculator->getTotals();
    }

    private function getCurrencyMock(string $currency): Currency
    {
        $mock = $this->createMock(Currency::class);
        $mock->method('getCode')
            ->willReturn($currency);

        return $mock;
    }

    private function getFakeData(): array
    {
        return [
            [
                'Customer' => 'Vendor 1',
                'Document number' => 1000000257,
                'Type' => 1,
                'Parent document' => '',
                'Currency' => 'USD',
                'Total' => 400,
            ],
            [
                'Customer' => 'Vendor 1',
                'Document number' => 1000000258,
                'Type' => 2,
                'Parent document' => 1000000257,
                'Currency' => 'EUR',
                'Total' => 100,
            ],
            [
                'Customer' => 'Vendor 1',
                'Document number' => 1000000259,
                'Type' => 3,
                'Parent document' => 1000000257,
                'Currency' => 'GBP',
                'Total' => 50,
            ],
            [
                'Customer' => 'Vendor 2',
                'Document number' => 1000000260,
                'Type' => 1,
                'Parent document' => '',
                'Currency' => 'EUR',
                'Total' => 900,
            ],
            [
                'Customer' => 'Vendor 2',
                'Document number' => 1000000261,
                'Type' => 1,
                'Parent document' => '',
                'Currency' => 'BGN',
                'Total' => 800,
            ],
        ];
    }
}
