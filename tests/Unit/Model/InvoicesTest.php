<?php
declare(strict_types=1);

namespace Unit\Model;

use Finance\Model\CurrencyConverter;
use Finance\Model\Data\DataSourceInterface;
use Finance\Model\Exception\ExchangeRateFormatException;
use Finance\Model\Exception\InvoiceException;
use Finance\Model\Invoices;
use PHPUnit\Framework\TestCase;

class InvoicesTest extends TestCase
{
    public function testCanLoadInvoices()
    {
        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataSource->method('getData')
            ->willReturn($this->getTestData());

        $this->assertEquals((new Invoices($dataSource))->getInvoices(), $this->getTestData());
    }

    public function testInvalidParentNumberThrowsException()
    {
        $testData = $this->getTestData();
        $testData[1]['Parent document'] = '999';

        $dataSource = $this->createMock(DataSourceInterface::class);
        $dataSource->method('getData')
            ->willReturn($testData);

        $this->expectException(InvoiceException::class);
        new Invoices($dataSource);
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
                'Total' => '400'
            ],
            [
                'Customer' => 'Vendor 2',
                'Vat number' => '555555',
                'Document number' => '1000000259',
                'Type' => '2',
                'Parent document' => '',
                'Currency' => 'EUR',
                'Total' => '300'
            ]
        ];
    }
}