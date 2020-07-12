<?php


namespace App\Test\Reader;

use App\Reader\CsvReader;
use PHPUnit\Framework\TestCase;

/**
 * Class CsvReaderTest
 * @package App\Test\Reader
 */
class CsvReaderTest extends TestCase
{
    /** @var CsvReader */
    private $reader;

    protected function setUp(): void
    {
        $this->reader = new CsvReader();
    }

    public function testGetData()
    {
        $data = $this->reader->getData(ROOT . '/uploads/tests/import.csv');

        $this->assertCount(3, $data);

        $this->assertContains(['Customer 1', '123456789', '1000000257', '1', '', 'USD', '400'], $data);
        $this->assertContains(['Customer 2', '987654321', '1000000258', '1', '', 'EUR', '900'], $data);
        $this->assertContains(['Customer 2', '123465123', '1000000259', '2', '987654321', 'EUR', '400'], $data);
    }

    public function testGetDataThrowsExceptionForNonExistingFile()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File not found');

        $this->reader->getData(ROOT . '/some/invalid/file');
    }
}
