<?php

namespace App\Models\Domain\Calculator;

use App\Exceptions\CurrencyException;
use App\Exceptions\DocumentException;
use App\Models\Domain\Currency\CurrencyConverter;
use App\Models\Domain\Filter\Filter;
use App\Models\Value\Column;
use App\Models\Value\Currency;

class Calculator
{
    /**
     * Array containing documents to be processed.
     *
     * @var array
     */
    private $documents;

    /**
     * Used for converting one currency to another.
     *
     * @var CurrencyConverter
     */
    private $converter;

    /**
     * Used for filtering documents based on given criteria.
     *
     * @var Filter
     */
    private $filter;

    /**
     * Currency which will be used for calculating the total amount of the documents.
     *
     * @var string
     */
    private $outputCurrency;

    /**
     * Calculator constructor.
     *
     * @param array $data
     * @param Currency $outputCurrency
     * @param CurrencyConverter $converter
     * @param Filter $filter
     * @throws DocumentException
     */
    public function __construct(array $data, Currency $outputCurrency, CurrencyConverter $converter, Filter $filter)
    {
        $this->documents = $data;
        $this->outputCurrency = $outputCurrency->getCode();
        $this->converter = $converter;
        $this->filter = $filter;

        $this->applyFilters();
        $this->validateParentIds();
    }

    /**
     *
     *
     * @return array
     * @throws CurrencyException
     */
    public function getTotals(): array
    {
        $totals = [];
        foreach ($this->documents as $document) {
            $customer = $document[Column::CUSTOMER];

            if (!isset($totals[$customer])) {
                $totals[$customer] = 0;
            }

            $amount = $this->getConvertedAmount($document[Column::TOTAL], $document[Column::CURRENCY]);

            switch ($document[Column::TYPE]) {
                case 1:
                case 3:
                    $totals[$customer] += $amount;
                    break;
                case 2:
                    $totals[$customer] -= $amount;
                    break;
            }
        }

        return $totals;
    }

    /**
     * Converts a given currency to the output currency.
     *
     * @param float $total
     * @param string $currency
     * @return float
     * @throws CurrencyException
     */
    private function getConvertedAmount(float $total, string $currency): float
    {
        return $this->converter->convert($total, $currency, $this->outputCurrency);
    }

    /**
     * Applies given filters to the documents array removing any records that do not match.
     *
     * @throws DocumentException
     */
    private function applyFilters(): void
    {
        $this->documents = $this->filter->apply($this->documents);
    }

    /**
     * Makes sure that there are no documents with parent IDs that are missing in the documents list.
     *
     * @throws DocumentException
     */
    private function validateParentIds(): void
    {
        $ids = array_column($this->documents, Column::DOCUMENT);
        $parentIds = array_filter(array_column($this->documents, Column::PARENT_DOCUMENT));
        $diff = array_diff($parentIds, $ids);

        if (!empty($diff)) {
            throw DocumentException::missingParent(array_unique($diff));
        }
    }
}
