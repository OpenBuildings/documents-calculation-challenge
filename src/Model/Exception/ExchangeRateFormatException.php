<?php

namespace Finance\Model\Exception;


class  ExchangeRateFormatException extends \Exception
{
    const MISSING_COLON = 1;
    const INVALID_CURRENCY_FORMAT = 3;
    const INVALID_CURRENCY = 4;
    const INVALID_EXCHANGE_RATE = 5;
    const MISSING_BASE_CURRENCY = 6;

    public static function missingColon()
    {
        return new self(
            'Colon is missing in the exchange rates argument',
            self::MISSING_COLON
        );
    }

    public static function invalidCurrencyFormat($currency)
    {
        return new self(
            'The currency does not consist of three uppercase letters ' . serialize($currency),
            self::INVALID_CURRENCY_FORMAT
        );
    }

    public static function unsupportedCurrency($currency)
    {
        return new self(
            'The specified currency is not supported ' . serialize($currency),
            self::INVALID_CURRENCY
        );
    }

    public static function invalidExchangeRate($rate)
    {
        return new self(
            'The specified exchange rate does not contain numeric value ' . serialize($rate),
            self::INVALID_EXCHANGE_RATE
        );
    }

    public static function missingBaseCurrency()
    {
        return new self(
            'There is no base currency defined in the exchange rates',
            self::MISSING_BASE_CURRENCY
        );
    }
}
