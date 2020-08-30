<?php

namespace App\Models\Domain\Currency;

use App\Exceptions\CurrencyException;
use App\Models\Value\Currency;

class CurrencyConverter
{
    /**
     * Currency to conversion rate map.
     *
     * @var array
     */
    private $currency2Rate;

    /**
     * CurrencyConverter constructor.
     *
     * @param Currency[] $currencies
     */
    public function __construct(array $currencies)
    {
        foreach ($currencies as $currency) {
            $code = $currency->getCode();
            $rate = $currency->getRate();
            $this->currency2Rate[$code] = $rate;
        }
    }

    /**
     * Convert one currency to another. Exception is thrown is an unsupported currency is provided.
     *
     * @param float $amount
     * @param string $from
     * @param string $to
     * @return float
     * @throws CurrencyException
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if (!isset($this->currency2Rate[$from])) {
            throw CurrencyException::unsupportedCurrency($from);
        }

        if (!isset($this->currency2Rate[$to])) {
            throw CurrencyException::unsupportedCurrency($to);
        }

        $rate = $this->currency2Rate[$from] / $this->currency2Rate[$to];
        return round($amount / $rate, 2);
    }
}
