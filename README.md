# [Clippings.com](https://clippings.com) invoicing command challenge

Clippings challenges you to create a PHP / JS application that lets you sum invoice documents in different currencies via a file.

This is a small task to evaluate potential hires.

## The task

We have a **CSV** file, containing a list of invoices, debit and credit notes in different
currencies. **Document structure** with **demo data** can be found in the [`data.csv` file](./data.csv).

Create a basic user interface, which allows you to:
 - Upload the CSV file
 - Input a list of currencies and exchange rates (for example: `EUR:1,USD:0.987,GBP:0.878`)
 - Define an output currency (for example: `GBP`)
 - Filter by a specific customer by VAT number (as an optional input)

Submitting the form should **return the sum of all documents**. If the optional input filter is used, the functionality should **return only the sum of the
invoices for that specific customer**.

The currencies can have different exchange rates, based on a default currency: `EUR:GBP, EUR:BGN` and so on. The default currency is specified by giving it an exchange rate of 1. EUR is used as a default currency only for the example.

Invoice types:
- 1 = invoice
- 2 = credit note
- 3 = debit note 

Note, that if we have a credit note, it should subtract from the total of the invoice and if we have a debit note, it should add to the sum of the invoice.


## Requirements

- Feel free to use any modern JavaScript framework for the Front end part of the task (e.g. React.js, Angular.js, etc.) - it is a requirement only for Frontend and Fullstack developer roles.
- The application MUST use only in memory storage.
- The application MUST comply to the PSR-2 coding standard and use a PSR-4 autoloader.
- The application MUST be covered by unit tests.
- The application MUST support different currencies.
- The application MUST validate the input (for example: show an error if an unsupported currency is passed; show an error if a document has a specified parent, but the parent is missing, etc.)
- Best OOP practices MUST be followed.

## Example usage

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
Customer Test - 147.58 USD
Customer Clippings - 180.89 USD
```