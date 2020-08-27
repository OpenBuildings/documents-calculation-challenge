<?php

namespace App;

use Interfaces\ValidatorInterface;

class CurrencyParser implements ValidatorInterface
{
    private $currencies;

    /**
     * @param mixed $data
     * @return CurrencyParser
     * @throws \Exception
     */
    public function validate($data): CurrencyParser
    {
        // if missing currencies or they are with trailing comma (means wrong format)
        if (!$data || substr($data, -strlen(',')) === ',') {
            throw new \Exception("Pass currencies as comma separated list on CLI!");
        }

        $this->currencies = explode(',', $data);
        return $this;
    }

    /**
     * @param null|string $output
     * @return null|string
     * @throws \Exception
     */
    public function validateOutputCurrency(?string $output)
    {
        if (!$output) {
            throw new \Exception("Output currency is a mandatory parameter");
        }
        if (strlen($output) < 3) {
            throw new \Exception("Currency must be a valid ISO code!");
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
