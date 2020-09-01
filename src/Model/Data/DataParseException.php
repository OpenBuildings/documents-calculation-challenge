<?php

namespace Finance\Model\Data;


class  DataParseException extends \Exception
{
    const CAN_NOT_OPEN_FILE = 1;
    const COLUMNS_COUNT_DOES_NOT_MATCH = 2;

    public static function canNotOpenFile(string $filename)
    {
        return new self(
            'Can not open file for reading: ' . $filename,
            self::CAN_NOT_OPEN_FILE
        );
    }

    public static function columnsCountDoesNotMatch($row)
    {
        return new self(
            'Columns count does not match headers: ' . serialize($row),
            self::COLUMNS_COUNT_DOES_NOT_MATCH
        );
    }
}
