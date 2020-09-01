<?php

namespace Finance\Model;

use Finance\Model\Data\DataSourceInterface;
use Finance\Model\Exception\InvoiceException;

/**
 * Invoices class is used for validation of data received from the specified data source
 * @package Finance\Model
 */
class Invoices
{
    // columns constants
    const CUSTOMER = 'Customer';
    const VAT = 'Vat number';
    const DOCUMENT_NUMBER = 'Document number';
    const TYPE = 'Type';
    const PARENT_DOCUMENT_NUMBER = 'Parent document';
    const CURRENCY = 'Currency';
    const TOTAL = 'Total';

    // document type constants
    const TYPE_INVOICE_ID = 1;
    const TYPE_CREDIT_NOTE_ID = 2;
    const TYPE_DEBIT_NOTE_ID = 3;

    /**
     * @var array
     */
    private $invoices;

    /**
     * Invoices constructor.
     * @param DataSourceInterface $dataSource
     */
    public function __construct(DataSourceInterface $dataSource)
    {
        $this->load($dataSource->getData());
    }

    /**
     * Load invoices from a list of associative arrays
     *
     * @param array $data
     */
    private function load(array $data)
    {
        $this->ensureParentNumbersAreValid($data);
        $this->invoices = $data;
    }

    /**
     * @param array $data
     */
    private function ensureParentNumbersAreValid(array $data)
    {
        $numbers = [];
        foreach ($data as $invoice) {
            $numbers[$invoice[Invoices::DOCUMENT_NUMBER]] = $invoice[Invoices::DOCUMENT_NUMBER];
        }

        foreach ($data as $invoice) {
            $parentNumber = $invoice[Invoices::PARENT_DOCUMENT_NUMBER];
            if ($parentNumber && !isset($numbers[$parentNumber])) {
                throw InvoiceException::invalidParentNumber($parentNumber);
            }
        }
    }

    /**
     * @return array List of invoices
     */
    public function getInvoices()
    {
        return $this->invoices;
    }
}
