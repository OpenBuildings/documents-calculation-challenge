<?php

namespace Finance\Model\Exception;

class  CalculatorException extends \Exception
{
    const INVALID_INVOICE_TYPE = 1;
    const INVALID_TOTAL_AMOUNT = 2;

    public static function invalidInvoiceType($invoiceType)
    {
        return new self(
            'The specified invoice type is invalid: ' . serialize($invoiceType),
            self::INVALID_INVOICE_TYPE
        );
    }

    public static function invalidTotalAmountForCustomer($amount, string $customer)
    {
        return new self(
            'The total amount for customer ' . $customer . ' is invalid: ' . $amount,
            self::INVALID_TOTAL_AMOUNT
        );
    }
}
