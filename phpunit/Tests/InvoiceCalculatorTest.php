<?php

use App\InvoiceCalculator;
use App\Currency;

class InvoiceCalculatorTest extends \PHPUnit\Framework\TestCase
{
    private $csvData = [
        [
            'Customer' => "Vendor 1",
            'Vat number' => "123456789",
            'Document number' => "1000000264",
            'Type' => "1",
            'Parent document' => "1000000264",
            'Currency' => "EUR",
            'Total' => "1600",
        ],
        [
            'Customer' => "Vendor 2",
            'Vat number' => "987654321",
            'Document number' => "1000000265",
            'Type' => "2",
            'Parent document' => "1000000265",
            'Currency' => "USD",
            'Total' => "600",
        ]
    ];
    private $currencies = [];
    protected $instance;

    protected function setUp(): void
    {
        Currency::$supportedCurrencies = ['EUR', 'USD', 'GBP'];
        for ($i = 0; $i < 3; $i++) {
            Currency::$supportedCurrencies = ['EUR', 'USD', 'GBP'];
            $this->currencies[Currency::$supportedCurrencies[$i]] = new Currency(
                Currency::$supportedCurrencies[$i], 0.6
            );
        }

        InvoiceCalculator::$allDocumentIds = ["1000000264", "1000000265"];
        $this->instance = InvoiceCalculator::getInstance($this->csvData, $this->currencies);
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf(InvoiceCalculator::class, $this->instance);
        $this->assertNotInstanceOf(\App\CurrencyParser::class, $this->instance);
    }

    public function testGetTotalsNoVat()
    {
        $totals = $this->instance->getTotals();
        $this->assertIsArray($totals);
        $this->assertNotEmpty($totals);
        $this->assertEquals(count($totals), 2);
    }

    public function testGetTotalsWithVat()
    {
        $totals = $this->instance->getTotals("123456789");
        $this->assertIsArray($totals);
        $this->assertNotEmpty($totals);
        $this->assertEquals(count($totals), 1);
        $this->assertEquals([
            'Vendor 1' => 960.0
        ], $totals);
    }

    public function testGetTotalsWithNotFoundParentVat()
    {
        $this->csvData = [
            [
                'Customer' => "Vendor 1",
                'Vat number' => "987654321",
                'Document number' => "1000000265",
                'Type' => "2",
                'Parent document' => "66666666", // non existent parent document
                'Currency' => "USD",
                'Total' => "600",
            ]
        ];
        InvoiceCalculator::$allDocumentIds = ["1000000264", "1000000265"];
        $this->instance = InvoiceCalculator::getInstance($this->csvData, $this->currencies);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invoice has parent document which is not found!');

        $this->instance->getTotals();
    }

    public function testGetTotalsWithCreditExceedingInvoice()
    {
        $this->csvData = [
            [
                'Customer' => "Vendor 1",
                'Vat number' => "987654321",
                'Document number' => "1000000265",
                'Type' => "2",
                'Parent document' => "1000000264",
                'Currency' => "USD",
                'Total' => "11600", // huge credit
            ]
        ];
        InvoiceCalculator::$allDocumentIds = ["1000000264", "1000000265"];
        $this->instance = InvoiceCalculator::getInstance($this->csvData, $this->currencies);

        // when credit is > than invoice it becomes negative balance, thus to be returned to the client
        $this->assertEquals($this->instance->getTotals()['Vendor 1'], -6960.0);
    }

    public function testPrintCalculatedTotals()
    {
        $totals = $this->instance->getTotals();
        $this->expectOutputString('Vendor 1: 1600EUR ' . PHP_EOL . 'Vendor 2: -600EUR ' . PHP_EOL);
        $this->instance->printCalculatedTotals($totals, 'EUR');
    }

    public function testPrintCalculatedTotalsWithVat()
    {
        $totals = $this->instance->getTotals("123456789");
        $this->expectOutputString('Vendor 1: 1600GBP ' . PHP_EOL);
        $this->instance->printCalculatedTotals($totals, 'GBP');
    }

    public function testPrintCalculatedTotalsWithoutOutputCurrency()
    {
        $totals = $this->instance->getTotals();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Output currency is a mandatory parameter!');

        $this->instance->printCalculatedTotals($totals, null);
    }

    protected function tearDown(): void
    {
        $this->csvData = null;
        $this->currencies = null;
        $this->instance = null;
    }
}
