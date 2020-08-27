<?php

namespace App;

use Interfaces\ValidatorInterface;

class CurrencyParser implements ValidatorInterface
{
    private $currencies;

    /**
     * @param mixed $data
     * @return mixed
     */
    public function validate($data): CurrencyParser
    {
        // if missing currencies or they are with trailing comma (means wrong format)
        if (!$data || substr($data, -strlen(',')) === ',') {
            echo "Pass currencies as comma separated list on CLI!\n";
            exit(1);
        }

        $this->currencies = explode(',', $data);
        return $this;
    }

    /**
     * @param null|string $output
     * @return null|string
     */
    public function validateOutputCurrency(?string $output)
    {
        if (!$output) {
            echo "Output currency is a mandatory parameter";
            exit(1);
        }
        if (strlen($output) < 3) {
            echo "Currency must be a valid ISO code!";
            exit(1);
        }

        return $output;
    }

    /**
     * @return array
     */
    public function parseCurrencies(): array
    {
        $parsedCurrencies = [];
        foreach ($this->currencies as $currency) {
            $pair = explode(':', $currency);
            $parsedCurrencies[$pair[0]] = $pair[1];
        }

        return $parsedCurrencies;
    }
}
