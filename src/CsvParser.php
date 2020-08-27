<?php

namespace App;

use Interfaces\ValidatorInterface;

class CsvParser implements ValidatorInterface
{
    private $csv = [];

    /**
     * @return array
     */
    public function parseCsvData(): array
    {
        $rows = array_map('str_getcsv', $this->csv);
        $header = array_shift($rows);
        $data = [];

        for ($i = 0; $i < count($rows); $i++) {
            $data[] = array_combine($header, $rows[$i]);
            // use this loop to keep all document ids as static property
            $this->parseAllDocumentIdsFromCsv($data[$i]);
        }

        return $data;
    }

    /**
     * Fill the InvoiceCalculator::$allDocumentIds to be used for validation later
     *
     * @param array $row
     */
    private function parseAllDocumentIdsFromCsv(array $row)
    {
        InvoiceCalculator::$allDocumentIds[] = (int)$row['Document number'];
    }

    /**
     * @param mixed $data
     * @return CsvParser
     */
    public function validate($data): CsvParser
    {
        $dataFile = file($data);
        if (!$dataFile || !strpos($data, '.csv')) {
            echo "CSV file not found or invalid format!";
            exit(1);
        }

        $this->csv = $dataFile;
        return $this;
    }
}
