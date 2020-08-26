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

/**
 * @param array|null $csv
 * @return array
 * @throws Exception
 */
function parseCsvData(?array $csv): array
{
    if (empty($csv)) {
        throw new \Exception("CSV data file not found!");
    }

    $rows = array_map('str_getcsv', $csv);
    $header = array_shift($rows);
    $data = [];

    for ($i = 0; $i < count($rows); $i++) {
        $data[] = array_combine($header, $rows[$i]);
        // use this loop to keep all document ids as static property
        \App\InvoiceCalculator::$allDocumentIds[] = (int)$data[$i]['Document number'];
    }

    return $data;
}
