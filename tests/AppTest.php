<?php


namespace App\Test;

use App\App;
use App\Cli\Command\CliCommandInterface;
use App\Cli\Command\CommandFactoryInterface;
use App\Output\PresentableInterface;
use App\Output\PrinterInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class AppTest
 * @package Tests
 */
class AppTest extends TestCase
{
    /** @var App */
    private $app;
    /** @var CommandFactoryInterface|MockInterface */
    private $commandFactoryMock;
    /** @var PrinterInterface|MockInterface */
    private $printerMock;

    protected function setUp(): void
    {
        $this->commandFactoryMock = \Mockery::mock(CommandFactoryInterface::class);
        $this->printerMock = \Mockery::mock(PrinterInterface::class);
        $this->app = new App($this->commandFactoryMock, $this->printerMock);
    }

    public function testRunCommand()
    {
        $commandMock = \Mockery::mock(CliCommandInterface::class);
        $this->commandFactoryMock->shouldReceive('getCommand')->andReturn($commandMock);
        $resultMock = \Mockery::mock(PresentableInterface::class);
        $commandMock->shouldReceive('run')->andReturn([$resultMock]);
        $this->printerMock->shouldReceive('print')->with($resultMock);
        $resultMock->shouldReceive('present')->andReturn('presented data');

        $this->expectNotToPerformAssertions();

        $this->app->runCommand('some-command', []);
    }

    public function testRunCommandPrintsErrorWhenCommandIsInvalid()
    {
        $this->commandFactoryMock->shouldReceive('getCommand')->andThrow(new \Exception('error'));
        $this->printerMock->shouldReceive('printError')->with('error');

        $this->expectNotToPerformAssertions();

        $this->app->runCommand('invalid-command', []);
    }
}
