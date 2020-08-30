<?php

namespace Unit\Models\Domain\Filter;

use App\Exceptions\DocumentException;
use App\Models\Domain\Filter\Filter;
use App\Models\Value\Column;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    private $filter;

    protected function setUp(): void
    {
        $this->filter = new Filter([
            $this->getColumnMock('first_name', 'John'),
            $this->getColumnMock('phone_number', '+359888888888'),
        ]);
    }

    public function testDataIsCorrectlyFiltered()
    {
        $filteredData = $this->filter->apply([
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone_number' => '+359888888888',
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Wick',
                'phone_number' => '+359888888888',
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone_number' => '+359888888889',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'phone_number' => '+359888888889',
            ],
        ]);

        $this->assertEquals([
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone_number' => '+359888888888',
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Wick',
                'phone_number' => '+359888888888',
            ],
        ], $filteredData);
    }

    public function testExceptionIsThrownWhenAnUnsupportedColumnIsUsed()
    {
        $this->expectException(DocumentException::class);
        $this->expectExceptionCode(DocumentException::UNSUPPORTED_COLUMN);

        $this->filter->apply([
            [
                'first_name' => 'Doe',
            ],
        ]);
    }

    private function getColumnMock(string $column, string $value): Column
    {
        $mock = $this->createMock(Column::class);
        $mock->method('getColumn')
            ->willReturn($column);
        $mock->method('getValue')
            ->willReturn($value);

        return $mock;
    }
}
