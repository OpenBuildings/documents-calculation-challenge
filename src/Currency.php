<?php

namespace App;

/**
 * Class Currency
 */
class Currency
{
    public static $supportedCurrencies = [];

    /**
     * @var string
     */
    private $currency;

    /**
     * @var int|float
     */
    private $rate;

    /**
     * Currency constructor.
     * @param string $currencyName
     * @param float $currencyRate
     * @throws \Exception
     */
    public function __construct(string $currencyName, float $currencyRate)
    {
        // validate if we have unsupported currency in config
        if (!in_array($currencyName, self::$supportedCurrencies)) {
            throw new \Exception("Unsupported currency: $currencyName");
        }

        $this->currency = $currencyName;
        $this->rate = $currencyRate;
    }

    /**
     * @return float|int
     */
    public function getRate()
    {
        return $this->rate;
    }
}
