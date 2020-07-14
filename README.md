# [Clippings.com](https://clippings.com) invoicing command challange

Clipping's challenges you to create a PHP console command, that lets you sum invoice documents in different currencies via a file.

This is a small task to evaluate potential hires.

## The task

We have a **CSV** file, containing a list of invoices and credit notes in different currencies. Create a ClI command taking the CSV file as an input, a list of currency and exchnage rates, an output currency, an optional param to specify and filter a specific customer and return the sum of all the documents.

If the optional param is passed, the command should return the sumed documents, only for that specified customer.

Note, that if we have a credit note, it should substract from the total of the invoice and if we have a debit note, it should add to the sum of the invoice.

## Some pointers

- The application MUST use only in memory storage.
- The application should comply to the PSR-2 coding standart and use a PSR-4 autoloader.
- The application MUST be covered by unit tests.
- The application MUST support different currencies and throw an exception, if an unsupported one is passed. The currencies can have different exchange rates, based on a default currency: EUR:GBP, EUR:BGN and so on.
- Make the calculation service usable as a stand alone component.
- The application must handle the case, where the total of all the credit notes is bigger than the sum of the invoice.
- The default currency is specified by giving it an exchange rate of 1, the EUR is used as a default currency only for the example.
- The application should throw an error, if a document has a specified parent, but the parent is missing.

## Example usage

Command:

```bash
./console import path-to-file/import.csv EUR:1,USD:0.987,GBP:0.878 GBP --vat=123456789
```

Class usage:

```php
$instance->setData($fileData);
$instance->setCurrencies([
    new Currency('EUR', 1),
    new Currency('USD', 0.987),
    new Currency('GBP', 0.878),
]);
$instance->getTotals($vat = ''): array;
```

Example output:

```
Customer Test - 147.58 EUR
Customer Clippings - 180.89 ERU
```

## Demo data

The demo data can be found in the [`data.csv` file](./data.csv).

Invoice types:

- 1 = invoice
- 2 = credit note
- 3 = debit note
