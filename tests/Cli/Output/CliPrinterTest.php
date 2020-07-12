<?php


namespace App\Test\Cli\Output;

use App\Cli\Output\CliPrinter;
use App\Output\PresentableInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class CliPrinterTest
 * @package App\Test\Cli\Output
 */
class CliPrinterTest extends TestCase
{
    /** @var CliPrinter */
    private $printer;

    protected function setUp(): void
    {
        $this->printer = new CliPrinter();
    }

    public function testPrintMessage()
    {
        $this->printer->printMessage('Some message');

        $this->expectOutputString("Some message\n");
    }

    public function testPrintError()
    {
        $this->printer->printError('Some error message');

        $this->expectOutputString("ERROR: Some error message\n");
    }

    public function testPrint()
    {
        $presentable = \Mockery::mock(PresentableInterface::class);
        $presentable->shouldReceive('present')->andReturn('Some presented data');
        $this->printer->print($presentable);

        $this->expectOutputString("Some presented data\n");
    }
}
