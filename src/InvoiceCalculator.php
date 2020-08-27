<?php

namespace App;

use Interfaces\CalculatorFactoryInterface;

/**
 * Class InvoiceCalculator
 * @package App
 */
class InvoiceCalculator implements CalculatorFactoryInterface
{
    /**
     * All supported currencies
     *
     * @var array
     */
    private $currencies = [];

    /**
     * @var array
     */
    private $csvData = [];

    /**
     * @var array
     */
    public static $allDocumentIds = [];

    /**
     * @param array $csvData
     * @param array $currencies
     * @return InvoiceCalculator
     */
    public static function getInstance(
        array $csvData,
        array $currencies
    ): InvoiceCalculator {
        $instance = new self();
        $instance->setData($csvData);
        $instance->setCurrencies($currencies);

        return $instance;
    }

    /**
     * InvoiceCalculator constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param array $csvData
     */
    private function setData(array $csvData): void
    {
        $this->csvData = $csvData;
    }

    /**
     * @param array $currency
     */
    private function setCurrencies(array $currency): void
    {
        $this->currencies = $currency;
    }

    /**
     * @param null $vatId
     * @return array|float
     * @throws \Exception
     */
    public function getTotals($vatId = null): array
    {
        $totalSums = [];
        $vatToCustomerMap = [];
        foreach ($this->csvData as $invoiceData) {
            if (!in_array($invoiceData['Parent document'], self::$allDocumentIds)
                && $invoiceData['Parent document'] !== '') {
                throw new \Exception("Invoice has parent document which is not found!");
            }

            $this->mapVatToCustomer($invoiceData, $vatToCustomerMap);

            if (!isset($totalSums[$invoiceData['Customer']])) {
                $totalSums[$invoiceData['Customer']] = 0; // add customer if not in totalSum
            }

            $this->calculatePerCustomer($totalSums, $invoiceData);
        }

        if ($vatId) { // if vat is passed from CLI
            $customer = $vatToCustomerMap[$vatId];
            $totalSums = [$customer => $totalSums[$customer]];
        }

        return $totalSums;
    }

    /**
     * @param array $invoice
     * @param array $vatToCustomerMap
     */
    private function mapVatToCustomer(array $invoice, array &$vatToCustomerMap): void
    {
        $vatToCustomerMap[$invoice['Vat number']] = $invoice['Customer'];
    }

    /**
     * @param array $totalSums
     * @param array $invoice
     * @throws \Exception
     */
    private function calculatePerCustomer(array &$totalSums, array $invoice)
    {
        $currencyRate = $this->currencies[$invoice['Currency']]->getRate();

        switch ($invoice['Type']) {
            case '1': // invoice
            case '3': // debit
                $totalSums[$invoice['Customer']] += round($invoice['Total'] * $currencyRate, 2);
                break;
            case '2': // credit
                $totalSums[$invoice['Customer']] -= round($invoice['Total'] * $currencyRate, 2);
                break;
            default:
                throw new \Exception("Unsupported invoice type!");
        }
    }
}
