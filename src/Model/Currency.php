<?php


namespace App\Model;

/**
 * Class Currency
 */
class Currency
{
    /** @var string */
    private $name;
    /** @var float  */
    private $rate;

    public function __construct(string $name, float $rate)
    {
        $this->name = $name;
        $this->rate = $rate;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }
}
