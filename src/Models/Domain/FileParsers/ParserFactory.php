<?php

namespace App\Models\Domain\FileParsers;

use App\Exceptions\FileParserException;

class ParserFactory
{
    /**
     * Returns a new file parser instance suitable for the given file.
     *
     * @param string $filename
     * @return FileParser
     * @throws FileParserException
     */
    public static function create(string $filename): FileParser
    {
        $filetype = self::getFileType($filename);

        switch ($filetype) {
            case 'csv':
                return new CsvParser();
            default:
                throw FileParserException::unsupportedFileType($filename);
        }
    }

    /**
     * Returns a given file's filetype.
     *
     * @param string $filename
     * @return string
     */
    private static function getFileType(string $filename): string
    {
        $parts = explode('.', $filename);
        return strtolower(array_pop($parts));
    }
}
