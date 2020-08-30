<?php

namespace App\Models\Domain\Calculator;

use App\Exceptions\DocumentException;
use App\Models\Domain\Currency\CurrencyConverter;
use App\Models\Domain\Filter\Filter;
use App\Models\Value\Column;
use App\Models\Value\Currency;

class CalculatorBuilder
{
    /**
     * @var array
     */
    private $data;

    /**
     * List of supported currencies.
     *
     * @var Currency[]
     */
    private $currencies;

    /**
     * List of columns to be used for filtering the data.
     *
     * @var Column[]
     */
    private $columns = [];

    /**
     * Currency in which the output of the calculator will be presented.
     *
     * @var Currency
     */
    private $outputCurrency;

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param array $currencies
     * @return $this
     */
    public function setCurrencies(array $currencies): self
    {
        $this->currencies = $currencies;
        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setFilters(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @param Currency $currency
     * @return $this
     */
    public function setOutputCurrency(Currency $currency): self
    {
        $this->outputCurrency = $currency;
        return $this;
    }

    /**
     * Builds dependencies and returns a new instance of the Calculator class.
     *
     * @return Calculator
     * @throws DocumentException
     */
    public function build(): Calculator
    {
        return new Calculator(
            $this->data,
            $this->outputCurrency,
            new CurrencyConverter($this->currencies),
            new Filter($this->columns)
        );
    }
}
