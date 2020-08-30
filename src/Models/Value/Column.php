<?php

namespace App\Models\Value;

use App\Exceptions\DocumentException;

class Column
{
    const CUSTOMER = 'Customer';
    const VAT = 'Vat number';
    const DOCUMENT = 'Document number';
    const TYPE = 'Type';
    const PARENT_DOCUMENT = 'Parent document';
    const CURRENCY = 'Currency';
    const TOTAL = 'Total';

    /**
     * Column name.
     *
     * @var string
     */
    private $column;

    /**
     * Column value.
     *
     * @var string
     */
    private $value;

    /**
     * Filter constructor.
     *
     * @param string $column
     * @param string $value
     * @throws DocumentException
     */
    public function __construct(string $column, string $value)
    {
        if (!in_array($column, self::getSupportedColumns())) {
            throw DocumentException::unsupportedColumn($column);
        }

        $this->column = $column;
        $this->value = $value;
    }

    /**
     * Returns column name.
     *
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * Returns column value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns a list of supported columns.
     *
     * @return string[]
     */
    public static function getSupportedColumns(): array
    {
        return [
            self::CUSTOMER,
            self::VAT,
            self::DOCUMENT,
            self::TYPE,
            self::PARENT_DOCUMENT,
            self::CURRENCY,
            self::TOTAL,
        ];
    }
}
