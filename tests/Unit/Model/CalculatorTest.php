<?php
declare(strict_types=1);

namespace Unit\Model;

use Finance\Model\Calculator;
use Finance\Model\CurrencyConverter;
use Finance\Model\Exception\CalculatorException;
use Finance\Model\Invoices;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testCanReturnCorrectTotals()
    {
        $invoices = $this->createMock(Invoices::class);
        $invoices->method('getInvoices')
            ->willReturn($this->getTestData());

        $converter = $this->createMock(CurrencyConverter::class);
        $converter->expects($this->at(0))
            ->method('convert')
            ->with('100', 'USD', 'EUR')
            ->willReturn(200.0);

        $converter->expects($this->at(1))
            ->method('convert')
            ->with('200', 'EUR', 'EUR')
            ->willReturn(200.0);


        $totals = (new Calculator($invoices, $converter, 'EUR'))->getTotals('');

        $this->assertEquals($totals, [
            'Vendor 1' => [
                Calculator::CUSTOMER => 'Vendor 1',
                Calculator::TOTAL => 200,
                Calculator::CURRENCY => 'EUR',

            ],
            'Vendor 2' => [
                Calculator::CUSTOMER => 'Vendor 2',
                Calculator::TOTAL => 200,
                Calculator::CURRENCY => 'EUR',

            ],
        ]);
    }

    public function testCanFilterByVat()
    {
        $invoices = $this->createMock(Invoices::class);
        $invoices->method('getInvoices')
            ->willReturn($this->getTestData());

        $converter = $this->createMock(CurrencyConverter::class);
        $converter->method('convert')
            ->with('100', 'USD', 'EUR')
            ->willReturn(200.0);

        $totals = (new Calculator($invoices, $converter, 'EUR'))->getTotals('123456789');

        $this->assertEquals($totals, [
            'Vendor 1' => [
                Calculator::CUSTOMER => 'Vendor 1',
                Calculator::TOTAL => 200,
                Calculator::CURRENCY => 'EUR',

            ],
        ]);
    }

    public function testInvalidParentNumberThrowsException()
    {
        $testData = $this->getTestData();
        $testData[0]['Type'] = 2;

        $invoices = $this->createMock(Invoices::class);
        $invoices->method('getInvoices')
            ->willReturn($testData);

        $converter = $this->createMock(CurrencyConverter::class);
        $converter->method('convert')
            ->with('100', 'USD', 'EUR')
            ->willReturn(200.0);

        $this->expectException(CalculatorException::class);
        $totals = (new Calculator($invoices, $converter, 'EUR'))->getTotals('123456789');
    }

    public function testInvalidInvoiceTypeThrowsException()
    {
        $testData = $this->getTestData();
        $testData[0]['Type'] = 10;

        $invoices = $this->createMock(Invoices::class);
        $invoices->method('getInvoices')
            ->willReturn($testData);

        $converter = $this->createMock(CurrencyConverter::class);
        $converter->method('convert')
            ->with('100', 'USD', 'EUR')
            ->willReturn(200.0);

        $this->expectException(CalculatorException::class);
        $totals = (new Calculator($invoices, $converter, 'EUR'))->getTotals('123456789');
    }

    private function getTestData()
    {
        return [
            [
                'Customer' => 'Vendor 1',
                'Vat number' => '123456789',
                'Document number' => '1000000257',
                'Type' => '1',
                'Parent document' => '',
                'Currency' => 'USD',
                'Total' => '100'
            ],
            [
                'Customer' => 'Vendor 2',
                'Vat number' => '555555',
                'Document number' => '1000000259',
                'Type' => '1',
                'Parent document' => '',
                'Currency' => 'EUR',
                'Total' => '200'
            ],
            [
                'Customer' => 'Vendor 2',
                'Vat number' => '555555',
                'Document number' => '1000000260',
                'Type' => '2',
                'Parent document' => '',
                'Currency' => 'EUR',
                'Total' => '100'
            ]
        ];
    }
}