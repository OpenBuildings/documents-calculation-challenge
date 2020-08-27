<?php

namespace Interfaces;

interface CalculatorFactoryInterface
{
    /**
     * Builder method signature
     *
     * @param array $data
     * @param array $currencies
     * @return mixed
     */
    public static function getInstance(array $data, array $currencies);
}