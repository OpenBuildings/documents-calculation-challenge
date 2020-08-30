<?php

namespace App\Exceptions;

use Exception;

class DocumentException extends Exception
{
    const UNSUPPORTED_COLUMN = 1;
    const MISSING_PARENT = 2;

    public static function unsupportedColumn(string $column): self
    {
        return new self(
            'Column "' . $column . '" is not supported.',
            self::UNSUPPORTED_COLUMN
        );
    }

    public static function missingParent(array $parentIds): self
    {
        return new self(
            'The following parent documents are missing: ' . implode(', ', $parentIds) . '.',
            self::MISSING_PARENT
        );
    }
}
