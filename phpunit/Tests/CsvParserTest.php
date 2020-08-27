<?php

use App\CsvParser;

class CsvParserTest extends \PHPUnit\Framework\TestCase
{
    private $instance;

    protected function setUp(): void
    {
        $this->instance = new CsvParser();
    }

    public function testValidateSuccess()
    {
        $this->assertInstanceOf(CsvParser::class, $this->instance->validate('data.csv'));
    }

    public function testValidateWithoutFile()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("File path can't be null");

        $this->instance->validate(null);
    }

    public function testValidateWithNonCsvFile()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("File is not CSV!");

        $this->instance->validate(__DIR__ . '/test_resources/data.txt');
    }

    public function testParseCsvData()
    {
        $this->instance->validate('data.csv');
        $data = $this->instance->parseCsvData();
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }

    protected function tearDown(): void
    {
        $this->instance = null;
    }
}
