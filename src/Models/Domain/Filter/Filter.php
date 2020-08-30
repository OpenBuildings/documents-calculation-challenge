<?php

namespace App\Models\Domain\Filter;

use App\Exceptions\DocumentException;
use App\Models\Value\Column;

class Filter
{
    /**
     * Column to value mapping.
     *
     * @var array
     */
    private $column2Value = [];

    /**
     * Filter constructor.
     *
     * @param Column[] $columns
     */
    public function __construct(array $columns)
    {
        foreach ($columns as $column) {
            $this->column2Value[$column->getColumn()] = $column->getValue();
        }
    }

    /**
     * Filters out rows of data that do not match the given criteria.
     *
     * @param array $data
     * @return array
     * @throws DocumentException
     */
    public function apply(array $data): array
    {
        foreach ($data as $key => $row) {
            foreach ($this->column2Value as $column => $value) {
                if (!isset($row[$column])) {
                    throw DocumentException::unsupportedColumn($column);
                }

                if (strcasecmp($row[$column], $value) !== 0) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }
}
