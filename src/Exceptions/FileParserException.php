<?php

namespace App\Exceptions;

use Exception;

class FileParserException extends Exception
{
    const UNABLE_TO_READ_FILE = 1;
    const UNSUPPORTED_FILE_TYPE = 2;

    public static function unableToReadFile(string $filename): self
    {
        return new self(
            'Unable to read file ' . $filename . '.',
            self::UNABLE_TO_READ_FILE
        );
    }

    public static function unsupportedFileType(string $filename): self
    {
        return new self(
            'File "' . $filename . '" is an unsupported file type.' ,
            self::UNSUPPORTED_FILE_TYPE
        );
    }
}
