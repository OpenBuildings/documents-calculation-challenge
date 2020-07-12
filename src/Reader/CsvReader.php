<?php


namespace App\Reader;

/**
 * Class CsvReader
 * @package App\Reader
 */
class CsvReader implements FileReaderInterface
{
    /**
     * @inheritDoc
     */
    public function getData(string $file)
    {
        if (!file_exists($file)) {
            throw new \Exception('File not found');
        }

        $data = [];
        if (($handle = fopen($file, "r")) !== false) {
            while (($line = fgetcsv($handle)) !== false) {
                $data[] = $line;
            }
            fclose($handle);
        }

        unset($data[0]);

        return $data;
    }
}
