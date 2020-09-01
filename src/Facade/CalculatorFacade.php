<?php

namespace Finance\Facade;

use Finance\Model\Calculator;
use Finance\Model\CurrencyConverter;
use Finance\Model\Data\CsvDataSource;
use Finance\Model\Invoices;

/**
 * Class CalculatorFacade
 *
 * Usage:
 * <code>
 * $instance = new CalculatorFacade();
 * $instance->setData($fileData);
 * $instance->setCurrencies([
 *   'EUR' => 1,
 *   'USD' => 0.987,
 *   'GBP' => 0.878,
 * ]);
 * $instance->setOutputCurrency('USD');
 * $instance->getTotals('');
 * </code>
 * @package Finance\Facade
 */
class CalculatorFacade
{
    private $invoices;

    private $currencyConverter;
    private $outputCurrency;

    /**
     * @param string $fileData Path to CSV file containing data
     */
    public function setData(string $fileData)
    {
        $this->invoices = new Invoices(new CsvDataSource($fileData));
    }

    /**
     * @param array $currencies List of currencies in format: ['USD' => 1, 'EUR' => 1.32]
     */
    public function setCurrencies(array $currencies): void
    {
        $exchangeRates = '';
        foreach ($currencies as $currency => $value) {
            $exchangeRates .= ($exchangeRates ? ',' : '') . $currency . ':' . $value;
        }

        $this->currencyConverter = new CurrencyConverter($exchangeRates);
    }

    public function setOutputCurrency(string $outputCurrency): void
    {
        $this->outputCurrency = $outputCurrency;
    }

    public function getTotals($vat = ''): array
    {
        return (new Calculator($this->invoices, $this->currencyConverter, $this->outputCurrency))->getTotals($vat);
    }

}
