<?php

// global helper functions

/**
 * @param null|string $currencyString
 * @return array
 * @throws Exception
 */
function parseCurrencies(?string $currencyString): array
{
    $currencies = explode(',', $currencyString);
    if (!$currencies || count($currencies) == 1) {
        throw new \Exception(
            'Currencies not provided in CLI arguments or not comma separated!'
        );
    }

    $currenciesArray = [];
    foreach ($currencies as $currency) {
        $pair = explode(':', $currency);
        $currenciesArray[$pair[0]] = $pair[1];
    }

    return $currenciesArray;
}
