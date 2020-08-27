<?php

use App\CurrencyParser;

class CurrencyParserTest extends \PHPUnit\Framework\TestCase
{
    private $instance;

    protected function setUp(): void
    {
        $this->instance = new CurrencyParser();
    }

    public function testValidateSuccess()
    {
        $this->assertInstanceOf(CurrencyParser::class, $this->instance->validate('EUR:1,USD:0.85,GBP:1.12'));
    }

    public function testValidateWrongCurrencyFormat()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Pass currencies as comma separated list on CLI!');
        $this->instance->validate('EUR:1,USD:0.85,GBP:1.12,');
    }

    public function testValidateEmptyCurrency()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Pass currencies as comma separated list on CLI!');
        $this->instance->validate(null);
    }

    public function testValidateOutputCurrency()
    {
        $this->assertEquals('EUR', $this->instance->validateOutputCurrency('EUR'));
    }

    public function testValidateOutputCurrencyEmpty()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Output currency is a mandatory parameter');
        $this->assertEquals('EUR', $this->instance->validateOutputCurrency(null));
    }

    public function testValidateOutputCurrencyInvalidCurrencyCode()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Currency must be a valid ISO code!');
        $this->assertEquals('EUR', $this->instance->validateOutputCurrency('US'));
    }

    public function testParseCurrencies()
    {
        $this->instance->validate('EUR:1,USD:0.85');
        $data = $this->instance->parseCurrencies();
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }

    protected function tearDown(): void
    {
        $this->instance = null;
    }
}
