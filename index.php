<?php

require_once "vendor/autoload.php";

use App\Currency;
use App\InvoiceCalculator;

try {
    // parse CLI arguments
    $csvData = file($argv[1]);
    $currencies = parseCurrencies($argv[2]);
    $outputCurrency = $argv[3];
    $vatId = $argv[4] ?? null;

    // reuse the same array for currency objects
    foreach ($currencies as $code => $rate) {
        unset($currencies[$code]);
        $currencies[$code] = new Currency($code, $rate);
    }

    $instance = InvoiceCalculator::getInstance(
        $csvData,
        $currencies
    );

    foreach ($instance->getTotals($vatId) as $customer => $total) {
        echo "$customer: " . round($total / $currencies[$outputCurrency]->getRate(), 2) . "$outputCurrency \n";
    }
} catch (\Exception $e) {
    var_dump([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
}
