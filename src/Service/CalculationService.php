<?php


namespace App\Service;

use App\Model\Currency;

/**
 * Class CalculationService
 */
class CalculationService implements CalculationServiceInterface
{
    /** @var int */
    public const INVOICE_TYPE_INVOICE = 1;
    /** @var int */
    public const INVOICE_TYPE_CREDIT = 2;
    /** @var int */
    public const INVOICE_TYPE_DEBIT = 3;

    /** @var string */
    private $outputCurrency;
    /** @var string */
    private $defaultCurrency;
    /** @var array */
    private $data = [];
    /** @var array */
    private $currencyRates = [];
    /** @var array */
    private $customerCache = [];

    /**
     * @param string $outputCurrency
     */
    public function setOutputCurrency(string $outputCurrency): void
    {
        $this->outputCurrency = $outputCurrency;
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function setData(array $data): void
    {
        $indexedData = [];
        foreach ($data as $invoice) {
            list($customer, $vat, $number, $type, $parent, $currency, $total) = $invoice;

            $this->addToCustomerCache($vat, $customer);

            $indexedData[$vat][] = $this->getIndexedArray($number, $type, $parent, $currency, $total);
        }

        $this->validateData($indexedData);

        $this->data = $indexedData;
    }

    /**
     * @param Currency[] $currencies
     */
    public function setCurrencies(array $currencies): void
    {
        foreach ($currencies as $currency) {
            if ($currency->getRate() == 1) {
                $this->defaultCurrency = $currency->getName();
            }

            $this->currencyRates[$currency->getName()] = $currency->getRate();
        }
    }

    /**
     * @param string $vat
     * @return array
     * @throws \Exception
     */
    public function getTotals($vat = ''): array
    {
        $this->validatePresetData($vat);

        $totals = [];

        $relevantCustomers = empty($vat) ? $this->customerCache : [$vat => $this->customerCache[$vat]];

        foreach ($relevantCustomers as $vat => $customer) {
            $totals[$customer] = round($this->getCustomerTotal($vat), 2);
        }

        return $totals;
    }

    /**
     * @param string $vat
     * @param string $customer
     * @throws \Exception
     */
    private function addToCustomerCache($vat, $customer): void
    {
        if (empty($vat)) {
            throw new \Exception('Vat is required for all customers');
        }

        if (array_key_exists($vat, $this->customerCache) && $customer != $this->customerCache[$vat]) {
            throw new \Exception('Found different customers with the same vat');
        }

        $this->customerCache[$vat] = $customer;
    }

    /**
     * @param $number
     * @param $type
     * @param $parent
     * @param $currency
     * @param $total
     * @return array
     */
    private function getIndexedArray($number, $type, $parent, $currency, $total): array
    {
        return [
            'number' => $number,
            'type' => $type,
            'parent' => $parent,
            'currency' => $currency,
            'total' => $total,
        ];
    }

    /**
     * @param array $indexedData
     * @throws \Exception
     */
    private function validateData(array $indexedData)
    {
        $validTypes = [self::INVOICE_TYPE_INVOICE, self::INVOICE_TYPE_CREDIT, self::INVOICE_TYPE_DEBIT];

        foreach ($indexedData as $vat => $invoices) {
            foreach ($invoices as $invoice) {
                foreach ($invoice as $property => $value) {
                    if ($property == 'parent') {
                        continue;
                    }

                    if (empty($value)) {
                        throw new \Exception("All invoices should have a $property");
                    }
                }

                if (!in_array($invoice['type'], $validTypes)) {
                    throw new \Exception("Invoice {$invoice['number']} has an invalid type");
                }

                $this->validateParent($invoice, $invoices);
            }
        }
    }

    /**
     * @param array $invoice
     * @param array $invoices
     * @throws \Exception
     */
    private function validateParent(array $invoice, array $invoices): void
    {
        if (empty($invoice['parent']) && $invoice['type'] != self::INVOICE_TYPE_INVOICE) {
            throw new \Exception("Notice {$invoice['number']} does not have a parent");
        }

        if (!empty($invoice['parent']) && $invoice['type'] == self::INVOICE_TYPE_INVOICE) {
            throw new \Exception("Invoice {$invoice['number']} should not have a parent");
        }

        if (!empty($invoice['parent'])) {
            $this->validateDocumentExists($invoice['parent'], $invoices);
        }
    }

    /**
     * @param string $number
     * @param array $invoices
     * @throws \Exception
     */
    private function validateDocumentExists(string $number, array $invoices): void
    {
        $filtered = array_filter($invoices, function ($invoice) use ($number) {
            return $invoice['number'] == $number;
        });

        if (count($filtered) !== 1) {
            throw new \Exception("Document with number $number was not found");
        }
    }

    /**
     * @param string $vat
     * @throws \Exception
     */
    private function validatePresetData($vat = ''): void
    {
        if (empty($this->data)) {
            throw new \Exception('No data set');
        }

        if (empty($this->defaultCurrency)) {
            throw new \Exception('No default currency set');
        }

        if (empty($this->outputCurrency)) {
            throw new \Exception('No output currency set');
        }

        if (!array_key_exists($this->outputCurrency, $this->currencyRates)) {
            throw new \Exception('Invalid output currency');
        }

        if (!empty($vat) && !array_key_exists($vat, $this->customerCache)) {
            throw new \Exception('Vat not found');
        }
    }

    /**
     * @param string $vat
     * @return float
     * @throws \Exception
     */
    private function getCustomerTotal(string $vat): float
    {
        $invoices = $this->data[$vat];

        $total = 0;

        foreach ($invoices as $invoice) {
            if (!empty($invoice['parent'])) {
                continue;
            }

            if (!(array_key_exists($invoice['currency'], $this->currencyRates))) {
                throw new \Exception("Currency {$invoice['currency']} was not found");
            }

            $invoiceTotal = $this->getInvoiceTotalInDefaultCurrency($invoice['number'], $invoices);
            $total += ($invoiceTotal * $this->currencyRates[$this->outputCurrency]);
        }

        return $total;
    }

    /**
     * @param string $number
     * @param array $invoices
     * @return float
     * @throws \Exception
     */
    private function getInvoiceTotalInDefaultCurrency($number, $invoices): float
    {
        $total = array_reduce($invoices, function ($carry, $invoice) use ($number) {
            $isSame = $invoice['number'] == $number;
            $isChild = $invoice['parent'] == $number;
            if (!$isSame && !$isChild) {
                return $carry;
            }

            $ratedTotal = $invoice['total'] / $this->currencyRates[$invoice['currency']];

            return $carry + ($invoice['type'] == self::INVOICE_TYPE_CREDIT ? -$ratedTotal : $ratedTotal);
        }, 0);

        if ($total < 0) {
            throw new \Exception(
                "The total of credit notices is bigger that the total of the invoice ($number)"
            );
        }

        return $total;
    }
}
