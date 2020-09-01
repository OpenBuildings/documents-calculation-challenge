<?php

namespace Finance\Model;

use Finance\Model\Exception\CalculatorException;

class Calculator
{
    const CUSTOMER = 'customer';
    const TOTAL = 'total';
    const CURRENCY = 'currency';

    /**
     * @var array List of invoices
     */
    private $invoices;

    /**
     * @var CurrencyConverter
     */
    private $converter;

    /**
     * @var string Desired output currency
     */
    private $outputCurrency;

    /**
     * @var array The result of the calculation
     */
    private $totals;

    public function __construct(Invoices $invoices, CurrencyConverter $converter, string $outputCurrency)
    {
        $this->invoices = $invoices->getInvoices();
        $this->converter = $converter;
        $this->outputCurrency = $outputCurrency;
    }

    /**
     * @param string $vatFilter
     * @return array List of customers with calculated total documents amount
     */
    public function getTotals(string $vatFilter): array
    {
        $this->calculateTotals($vatFilter);
        $this->ensureAllTotalsArePositive();

        return $this->totals;
    }

    private function calculateTotals(string $vatFilter)
    {
        $totals = [];
        foreach ($this->invoices as $invoice) {
            $customer = $invoice[Invoices::CUSTOMER];
            if ($vatFilter && $vatFilter != $invoice[Invoices::VAT]) {
                continue;
            }

            if (empty($totals[$customer])) {
                $totals[$customer] = [
                    self::CUSTOMER => $customer,
                    self::TOTAL => 0,
                    self::CURRENCY => $this->outputCurrency
                ];
            }

            $totals[$customer][self::TOTAL] = $this->setAmount($totals[$customer][self::TOTAL], $invoice);
        }
        $this->totals = $totals;
    }

    /**
     * Set new amount by using the existing and adding the converted total according to the output currency
     *
     * @param float $calculatedTotal
     * @param array $invoice
     * @return float
     * @throws CalculatorException
     */
    private function setAmount($calculatedTotal, $invoice)
    {
        $convertedTotal = $this->converter->convert($invoice[Invoices::TOTAL], $invoice[Invoices::CURRENCY],
            $this->outputCurrency);

        switch ($invoice[Invoices::TYPE]) {
            case Invoices::TYPE_INVOICE_ID:
            case Invoices::TYPE_DEBIT_NOTE_ID:
                $calculatedTotal += $convertedTotal;
                break;
            case Invoices::TYPE_CREDIT_NOTE_ID:
                $calculatedTotal -= $convertedTotal;
                break;
            default:
                throw CalculatorException::invalidInvoiceType($invoice[Invoices::TYPE]);
        }
        return $calculatedTotal;
    }

    private function ensureAllTotalsArePositive()
    {
        array_walk($this->totals, function ($el) {
            if ($el[self::TOTAL] <= 0) {
                throw CalculatorException::invalidTotalAmountForCustomer($el[self::TOTAL], $el[self::CUSTOMER]);
            }
        });
    }
}
