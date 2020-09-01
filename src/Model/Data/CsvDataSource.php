<?php

namespace Finance\Model\Data;

/**
 * CsvDataSource loads data from CSV file which has the column names listed in the first row
 *
 * @package Finance\Model\Data
 */
class CsvDataSource implements DataSourceInterface
{
    /**
     * @var array
     */
    private $data = [];

    public function __construct(string $filePath)
    {
        $this->loadData($filePath);
    }

    /**
     * Load data from CSV file and return it as a list of associative arrays
     * @param string $filePath
     */
    public function loadData(string $filePath)
    {
        $this->data = [];

        if (($handle = fopen($filePath, "r")) === false) {
            throw DataParseException::canNotOpenFile($filePath);
        }

        $isFirstLine = true;
        $headers = [];
        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
            if ($isFirstLine) {
                $isFirstLine = false;
                $headers = $row;
                continue;
            }

            if (count($row) != count($headers)) {
                throw DataParseException::columnsCountDoesNotMatch($row);
            }

            $this->data[] = array_combine($headers, $row);
        }
        fclose($handle);
    }

    public function getData(): array
    {
        return $this->data;
    }
}
