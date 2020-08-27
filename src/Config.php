<?php

namespace App;

class Config
{
    private $config = [];

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->config = require_once __DIR__ . '/../configs/common.php';
    }

    /**
     * Access config data via magic method
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->config[$key] ?? null;
    }
}
