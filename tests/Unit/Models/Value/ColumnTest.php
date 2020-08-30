<?php

namespace Unit\Models\Value;

use App\Exceptions\DocumentException;
use App\Models\Value\Column;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    public function testExpectedDataIsReturned()
    {
        $column = new Column(Column::TYPE, 'value');

        $this->assertEquals(Column::TYPE, $column->getColumn());
        $this->assertEquals('value', $column->getValue());
    }

    public function testExceptionIsThrownIfAnUnsupportedColumnIsProvided()
    {
        $this->expectException(DocumentException::class);
        $this->expectExceptionCode(DocumentException::UNSUPPORTED_COLUMN);

        new Column('test', 'value');
    }
}
