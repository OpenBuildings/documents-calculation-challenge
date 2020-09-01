<?php

namespace Finance\Model\Exception;

class  InvoiceException extends \Exception
{
    const INVALID_PARENT_NUMBER = 1;

    public static function invalidParentNumber($parentNumber)
    {
        return new self(
            'The specified parent number is invalid: ' . $parentNumber,
            self::INVALID_PARENT_NUMBER
        );
    }
}
