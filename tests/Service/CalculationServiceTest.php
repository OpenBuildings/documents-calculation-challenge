<?php


namespace App\Test\Service;

use App\Model\Currency;
use App\Service\CalculationService;
use PHPUnit\Framework\TestCase;

/**
 * Class CalculationServiceTest
 * @package App\Test\Service
 */
class CalculationServiceTest extends TestCase
{
    /** @var CalculationService */
    private $service;

    protected function setUp(): void
    {
        $this->service = new CalculationService();
    }

    public function testSetDataThrowsExceptionForEmptyVat()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Vat is required for all customers');

        $this->service->setData([
            ['Customer 1', ''],
        ]);
    }

    public function testSetDataThrowsExceptionForDifferentCustomersWithSameVat()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Found different customers with the same vat');

        $this->service->setData([
            ['Customer 1', 'same-vat'],
            ['Customer 2', 'same-vat'],
        ]);
    }

    public function testSetDataThrowsExceptionForEmptyNumber()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('All invoices should have a number');


        $this->service->setData([
            ['Customer', 'vat', '', CalculationService::INVOICE_TYPE_INVOICE, '', 'BGN', 500],
        ]);
    }

    public function testSetDataThrowsExceptionForEmptyType()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('All invoices should have a type');

        $this->service->setData([
            ['Customer', 'vat', 'number', '', '', 'BGN', 500],
        ]);
    }

    public function testSetDataThrowsExceptionForInvalidType()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invoice 123123 has an invalid type');

        $this->service->setData([
            ['Customer', 'vat', '123123', 'type', '', 'BGN', 500],
        ]);
    }

    public function testSetDataThrowsExceptionForEmptyCurrency()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('All invoices should have a currency');

        $this->service->setData([
            ['Customer', 'vat', 'number', CalculationService::INVOICE_TYPE_INVOICE, '', '', 200],
        ]);
    }

    public function testSetDataThrowsExceptionForEmptyTotal()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('All invoices should have a total');

        $this->service->setData([
            ['Customer 1', 'vat', 'number', CalculationService::INVOICE_TYPE_INVOICE, '', 'EUR'],
        ]);
    }

    public function testSetDataThrowsExceptionForNoticeWithoutAParent()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Notice 43242432 does not have a parent');

        $this->service->setData([
            ['Customer', 'vat', '43242432', CalculationService::INVOICE_TYPE_DEBIT, '', 'EUR', 400],
        ]);
    }

    public function testSetDataThrowsExceptionForInvoiceWithAParent()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invoice 12312312 should not have a parent');

        $this->service->setData([
            ['Customer', 'vat', '12312312', CalculationService::INVOICE_TYPE_INVOICE, '99999999', 'BGN', 100],
        ]);
    }

    public function testSetDataThrowsExceptionForNoticeWithNonExistingParent()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Document with number 11111110 was not found');

        $this->service->setData([
            ['Customer', '12341234', '11111111', CalculationService::INVOICE_TYPE_CREDIT, '11111110', 'BGN', 40],
        ]);
    }

    public function testGetTotalsThrowsExceptionWithoutSetData()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No data set');

        $this->service->getTotals();
    }

    public function testGetTotalsThrowsExceptionWithoutADefaultCurrency()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No default currency set');

        $this->service->setData([
            ['Customer', '12341234', '11111111', CalculationService::INVOICE_TYPE_INVOICE, '', 'BGN', 40],
        ]);

        $this->service->getTotals();
    }

    public function testGetTotalsThrowsExceptionWithoutAnOutputCurrency()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No output currency set');

        $this->service->setCurrencies([new Currency('BGN', 1)]);
        $this->service->setData([
            ['Customer', '12341234', '11111111', CalculationService::INVOICE_TYPE_INVOICE, '', 'BGN', 40],
        ]);

        $this->service->getTotals();
    }

    public function testGetTotalsThrowsExceptionForInvalidOutputCurrency()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid output currency');

        $this->service->setOutputCurrency('BAD');
        $this->service->setCurrencies([new Currency('BGN', 1)]);
        $this->service->setData([
            ['Customer', '12341234', '11111111', CalculationService::INVOICE_TYPE_INVOICE, '', 'BGN', 40],
        ]);

        $this->service->getTotals();
    }

    public function testGetTotalsThrowsExceptionForInvalidFilterVat()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Vat not found');

        $this->service->setOutputCurrency('BGN');
        $this->service->setCurrencies([new Currency('BGN', 1)]);
        $this->service->setData([
            ['Customer', 'vat', '11111111', CalculationService::INVOICE_TYPE_INVOICE, '', 'BGN', 40],
        ]);

        $this->service->getTotals('bad vat');
    }

    public function testGetTotalsThrowsExceptionForInvoiceWithInvalidCurrency()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Currency EUR was not found');

        $this->service->setOutputCurrency('BGN');
        $this->service->setCurrencies([new Currency('BGN', 1)]);
        $this->service->setData([
            ['Customer', 'vat', '11111111', CalculationService::INVOICE_TYPE_INVOICE, '', 'EUR', 40],
        ]);

        $this->service->getTotals();
    }

    public function testGetTotalsThrowsExceptionForCreditNoticesWithGreaterTotalThanTheParentInvoice()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The total of credit notices is bigger that the total of the invoice (11111111)');

        $this->service->setOutputCurrency('EUR');
        $this->service->setCurrencies([new Currency('EUR', 1)]);
        $this->service->setData([
            ['Customer', 'vat', '11111111', CalculationService::INVOICE_TYPE_INVOICE, '', 'EUR', 40],
            ['Customer', 'vat', '11111112', CalculationService::INVOICE_TYPE_CREDIT, '11111111', 'EUR', 500],
        ]);

        $this->service->getTotals();
    }

    public function testGetTotals(){
        $this->service->setOutputCurrency('BGN');
        $this->service->setCurrencies([new Currency('EUR', 1), new Currency('BGN', 1.95)]);
        $this->service->setData([
            ['Customer', 'vat', '11111111', CalculationService::INVOICE_TYPE_INVOICE, '', 'EUR', 400],
            ['Customer', 'vat', '11111112', CalculationService::INVOICE_TYPE_CREDIT, '11111111', 'BGN', 195],
            ['Customer 2', 'vat2', '11111113', CalculationService::INVOICE_TYPE_INVOICE, '', 'EUR', 100],
            ['Customer 2', 'vat2', '11111114', CalculationService::INVOICE_TYPE_DEBIT, '11111113', 'EUR', 100],
        ]);

        $totals = $this->service->getTotals();

        $this->assertEquals(['Customer' => 585, 'Customer 2' => 390], $totals);
    }
}
