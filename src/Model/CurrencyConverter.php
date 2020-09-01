<?php

namespace Finance\Model;

use Finance\Model\Exception\ExchangeRateFormatException;

/**
 * CurrencyConverter is component for converting currency according to specified exchange rate
 * @package Finance\Model
 */
class CurrencyConverter
{
    /**
     * @var array
     */
    private $rates;

    /**
     * @var string
     */
    private $baseCurrency;

    /**
     * The exchange rates are provided as string in the following format: "EUR:1,USD:0.987,GBP:0.878"
     * The currency having value of "1" is considered to be base
     * @param string $exchangeRates
     */
    public function __construct(string $exchangeRates)
    {
        $this->load($exchangeRates);
    }

    private function load(string $exchangeRates)
    {
        $this->rates = [];
        $this->baseCurrency = null;

        foreach (explode(',', $exchangeRates) as $tuple) {
            if (strpos($tuple, ':') === false) {
                throw ExchangeRateFormatException::missingColon();
            }
            list($currency, $rate) = explode(':', $tuple);

            if (strlen($currency) != 3 || strtoupper($currency) != $currency) {
                throw ExchangeRateFormatException::invalidCurrencyFormat($currency);
            }

            if (!is_numeric($rate)) {
                throw ExchangeRateFormatException::invalidExchangeRate($rate);
            }

            $this->rates[$currency] = (float)$rate;
            if ($rate == 1) {
                $this->baseCurrency = $currency;
            }
        }

        if (empty($this->baseCurrency)) {
            throw ExchangeRateFormatException::missingBaseCurrency();
        }
    }


    /**
     * Convert specified amount from one currency to another and return the result
     *
     * @param float|int $amount
     * @param string $from
     * @param string $to
     * @return float
     * @throws ExchangeRateFormatException
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if (!isset($this->rates[$from])) {
            throw ExchangeRateFormatException::unsupportedCurrency($from);
        }

        if (!isset($this->rates[$to])) {
            throw ExchangeRateFormatException::unsupportedCurrency($to);
        }

        return ($amount * $this->rates[$from]) / $this->rates[$to];
    }
}
