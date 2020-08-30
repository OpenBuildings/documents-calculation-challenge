<?php

namespace App\Exceptions;

use Exception;

class CurrencyException extends Exception
{
    const UNSUPPORTED_CURRENCY = 1;

    public static function unsupportedCurrency(string $currencyCode): self
    {
        return new self(
            'Provided currency "' . $currencyCode . '" is not supported.',
            self::UNSUPPORTED_CURRENCY
        );
    }
}
