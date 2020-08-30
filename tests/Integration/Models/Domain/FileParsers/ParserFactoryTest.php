<?php

namespace Integration\Models\Domain\FileParsers;

use App\Exceptions\FileParserException;
use App\Models\Domain\FileParsers\ParserFactory;
use App\Models\Value\Column;
use PHPUnit\Framework\TestCase;

class ParserFactoryTest extends TestCase
{
    public function testCsvFileCanBeParsed()
    {
        $filename = dirname(__DIR__) . '../../../../data/data.csv';

        echo $filename;

        $parser = ParserFactory::create($filename);
        $data = $parser->parse($filename);

        $this->assertIsArray($data);
        $this->assertCount(8, $data);
        $this->assertArrayHasKey(Column::TYPE, $data[0]);
    }

    public function testExceptionsIsThrownIfAnUnsupportedFiletypeIsParsed()
    {
        $this->expectException(FileParserException::class);
        $this->expectExceptionCode(FileParserException::UNSUPPORTED_FILE_TYPE);

        $filename = 'data.txt';

        $parser = ParserFactory::create($filename);
        $parser->parse($filename);
    }
}
