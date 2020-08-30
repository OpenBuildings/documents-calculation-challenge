<?php

namespace App\Models\Domain\FileParsers;

use App\Exceptions\FileParserException;

class CsvParser implements FileParser
{
    const DELIMITER = ',';

    /**
     * Parses a CSV file and returns its contents as an array indexed by headers.
     *
     * @param string $filename
     * @return array
     * @throws FileParserException
     */
    public function parse(string $filename): array
    {
        if (($handle = fopen($filename, 'r')) === false) {
            throw FileParserException::unableToReadFile($filename);
        }

        $data = [];
        $gotHeaders = false;
        while ($row = fgetcsv($handle, 0, self::DELIMITER)) {
            if (!$gotHeaders) {
                $headers = $row;
                $gotHeaders = true;
                continue;
            }

            $data[] = array_combine($headers, $row);
        }
        fclose($handle);

        return $data;
    }
}
