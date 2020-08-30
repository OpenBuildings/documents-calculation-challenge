<?php

namespace App\Models\Value;

use App\Exceptions\CurrencyException;

class Currency
{
    const USD = 'USD';
    const EUR = 'EUR';
    const GBP = 'GBP';
    const BGN = 'BGN';

    /**
     * ISO country code.
     *
     * @var string
     */
    private $code;

    /**
     * Currency exchange rate.
     *
     * @var float
     */
    private $rate;

    /**
     * Currency constructor.
     *
     * @param string $code
     * @param float $rate
     * @throws CurrencyException
     */
    public function __construct(string $code, float $rate = 0)
    {
        if (!in_array($code, $this->getSupportedCurrencies())) {
            throw CurrencyException::unsupportedCurrency($code);
        }

        $this->code = $code;
        $this->rate = $rate;
    }

    /**
     * Returns ISO currency code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Returns currency exchange rate.
     *
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * Returns a list of all supported currencies.
     *
     * @return string[]
     */
    private function getSupportedCurrencies(): array
    {
        return [self::USD, self::EUR, self::GBP, self::BGN];
    }
}
