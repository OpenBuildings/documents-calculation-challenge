<?php
declare(strict_types=1);

namespace Integration\Model\Data;

use Finance\Model\CurrencyConverter;
use Finance\Model\Data\CsvDataSource;
use Finance\Model\Data\DataParseException;
use Finance\Model\Exception\ExchangeRateFormatException;
use PHPUnit\Framework\TestCase;

class CsvDataSourceTest extends TestCase
{
    public function testCsvCanBeLoaded()
    {
        $this->assertEquals(
            (new CsvDataSource(__DIR__ . '/../../../data/test.csv'))->getData(),
            [
                [
                    'Customer' => 'Vendor 1',
                    'Vat number' => '123456789',
                    'Document number' => '1000000257',
                    'Type' => '1',
                    'Parent document' => '',
                    'Currency' => 'USD',
                    'Total' => '400'
                ]
            ]
        );
    }


    public function testInvalidFilePath(): void
    {
        $this->expectException(DataParseException::class);
        (new CsvDataSource(__DIR__ . '/../../../data/invalid.csv'));
    }

    public function testInvalidHeaderrs(): void
    {
        $this->expectException(DataParseException::class);
        (new CsvDataSource(__DIR__ . '/../../../data/test_invalid_headers.csv'));
    }
}