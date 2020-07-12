<?php


namespace App\Test\Cli\Command;

use App\Cli\Command\ImportCommand;
use App\Model\CalculationResult;
use App\Model\Currency;
use App\Reader\FileReaderInterface;
use App\Service\CalculationServiceInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class ImportCommandTest
 * @package App\Test\Cli\Command
 */
class ImportCommandTest extends TestCase
{
    /** @var ImportCommand */
    private $command;
    /** @var CalculationServiceInterface|MockInterface */
    private $serviceMock;
    /** @var FileReaderInterface|MockInterface */
    private $fileReaderMock;

    protected function setUp(): void
    {
        $this->serviceMock = \Mockery::mock(CalculationServiceInterface::class);
        $this->fileReaderMock = \Mockery::mock(FileReaderInterface::class);
        $this->command = new ImportCommand($this->serviceMock, $this->fileReaderMock);
    }

    public function testRun()
    {
        $this->serviceMock->shouldReceive('setOutputCurrency')->with('EUR');
        $this->fileReaderMock->shouldReceive('getData')
            ->with(ROOT . '/path/to/file')
            ->andReturn(['some-data']);
        $this->serviceMock->shouldReceive('setData')->with(['some-data']);
        $this->serviceMock->shouldReceive('setCurrencies')->with(
            \Mockery::on(function ($args) {
                $countIsCorrect = count($args) == 2;
                $argsAreInstancesOfCurrency = $args[0] instanceof Currency && $args[1] instanceof Currency;
                $firstHasCorrectData = $args[0]->getName() == 'BGN' && $args[0]->getRate() == 1;
                $secondHasCorrectData = $args[1]->getName() == 'EUR' && $args[1]->getRate() == 1.95;

                return $countIsCorrect && $argsAreInstancesOfCurrency && $firstHasCorrectData && $secondHasCorrectData;
            })
        );
        $this->serviceMock->shouldReceive('getTotals')->with('vat')
            ->andReturn(['some-vendor' => 500]);

        $result = $this->command->run([
            'path/to/file',
            'BGN:1,EUR:1.95',
            'EUR',
            '--vat=vat',
        ]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(CalculationResult::class, $result[0]);
        $this->assertSame('some-vendor', $result[0]->getName());
        $this->assertEquals(500, $result[0]->getSum());
        $this->assertSame('EUR', $result[0]->getCurrency());
    }

    public function testRunThrowsExceptionForMissingOutputCurrency()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No output currency provided');

        $this->command->run([]);
    }

    public function testRunThrowsExceptionForInvalidCurrencyFormat()
    {
        $this->serviceMock->shouldReceive('setOutputCurrency');
        $this->serviceMock->shouldNotReceive('setData');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid currency format');

        $this->command->run([
            'path/to/file',
            'invalid-format',
            'EUR',
        ]);
    }

    public function testRunThrowsExceptionWhenTheFileReaderThrowsOne()
    {
        $this->serviceMock->shouldReceive('setOutputCurrency');
        $this->serviceMock->shouldReceive('setCurrencies');
        $this->serviceMock->shouldNotReceive('setData');

        $this->fileReaderMock->shouldReceive('getData')
            ->andThrow(new \Exception('Invalid file'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid file');

        $this->command->run([
            'invalid-file',
            'BGN:1,EUR:1.95',
            'EUR',
        ]);
    }

    public function testRunThrowsExceptionWhenSetDataThrowsOne()
    {
        $this->serviceMock->shouldReceive('setOutputCurrency');
        $this->serviceMock->shouldReceive('setCurrencies');
        $this->fileReaderMock->shouldReceive('getData')->andReturn([]);
        $this->serviceMock->shouldReceive('setData')->andThrow(new \Exception('Invalid data'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid data');

        $this->command->run([
            'path/to/file',
            'BGN:1,EUR:1.95',
            'EUR',
        ]);
    }

    public function testRunThrowsExceptionWhenGetTotalsThrowsOne()
    {
        $this->serviceMock->shouldReceive('setOutputCurrency');
        $this->serviceMock->shouldReceive('setCurrencies');
        $this->fileReaderMock->shouldReceive('getData')->andReturn([]);
        $this->serviceMock->shouldReceive('setData');
        $this->serviceMock->shouldReceive('getTotals')->andThrow(new \Exception('Nope'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Nope');

        $this->command->run([
            'path/to/file',
            'BGN:1,EUR:1.95',
            'EUR',
        ]);
    }
}
