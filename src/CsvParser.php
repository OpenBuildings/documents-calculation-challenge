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
     * @throws \Exception
     */
    public function validate($data): CsvParser
    {
        if (!$data) {
            throw new \Exception("File path can't be null");
        }
        $dataFile = file($data);
        if (!strpos($data, '.csv')) {
            throw new \Exception("File is not CSV!");
        }

        $this->csv = $dataFile;
        return $this;
    }
}
