<?php

require_once "vendor/autoload.php";

// mandatory parameters are 4
if (count($argv) < 4) {
    echo "Insufficient arguments!" . PHP_EOL;
    echo "Usage: php index.php /path/to/file.csv currency1:rate1,currency2:rate2,currency3:rate3, output_currency vat_id" . PHP_EOL;
    echo "@NOTE: vat_id is optional parameter" . PHP_EOL;
    
    exit(1);
}

use App\Currency;
use App\InvoiceCalculator;

$csvParser = new \App\CsvParser();
$currencyParser = new \App\CurrencyParser();
$config = new \App\Config();
Currency::$supportedCurrencies = $config->supported_currencies;

// parse CLI arguments
$csvData = $csvParser->validate($argv[1])->parseCsvData();
$currencies = $currencyParser->validate($argv[2])->parseCurrencies();
$outputCurrency = $currencyParser->validateOutputCurrency($argv[3]);
$vatId = $argv[4] ?? null; // non mandatory parameter

try {
    // reuse the same array for currency objects
    foreach ($currencies as $code => $rate) {
        unset($currencies[$code]);
        $currencies[$code] = new Currency($code, $rate);
    }

    $instance = InvoiceCalculator::getInstance($csvData, $currencies);
    $instance->printCalculatedTotals($instance->getTotals($vatId), $outputCurrency);
} catch (\Exception $e) {
    var_dump([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
}
