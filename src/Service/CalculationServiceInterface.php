<?php


namespace App\Service;

use App\Model\Currency;

/**
 * Interface CalculationServiceInterface
 */
interface CalculationServiceInterface
{
    /**
     * @param string $outputCurrency
     */
    public function setOutputCurrency(string $outputCurrency): void;

    /**
     * @param array $data
     */
    public function setData(array $data): void;

    /**
     * @param Currency[] $currencies
     */
    public function setCurrencies(array $currencies): void;

    /**
     * @param string $vat
     * @return array
     */
    public function getTotals($vat = ''): array;
}
