# [Clippings.com](https://clippings.com) invoicing command challange

Clipping's challenges you to create a PHP console command, that lets you sum invoice documents in different currencies via a file.

This is a small task to evaluate potential hires.

## Requirements
* PHP >= 7.2

## Installation

```
composer install
```

## Usage

Command:

```bash
php calculator import data.csv USD EUR:1 USD:1.19057 GBP:0.891710 --vat=123456789
```

Class usage:

```php
$calculator = (new CalculatorBuilder())
    ->setData($data)
    ->setCurrencies([
        new Currency('EUR', 1),        
        new Currency('USD', 1.19057),    
        new Currency('GBP', 0.891710),    
    ])
    ->setFilters([
        new Column('Vat number', 123456789)       
    ])
    ->setOutputCurrency(new Currency('EUR'))
    ->build();

$totals = $calculator->getTotals();
```

Example output:

```
Customer Test - 147.58 EUR
Customer Clippings - 180.89 ERU
```