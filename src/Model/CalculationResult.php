<?php


namespace App\Model;

use App\Output\PresentableInterface;

/**
 * Class CalculationResult
 * @package App\Model
 */
class CalculationResult implements PresentableInterface
{
    /** @var string */
    private $name;
    /** @var float */
    private $sum;
    /** @var string */
    private $currency;

    /**
     * CalculationResult constructor.
     * @param string $name
     * @param float $sum
     * @param string $currency
     */
    public function __construct(string $name, float $sum, string $currency)
    {
        $this->name = $name;
        $this->sum = $sum;
        $this->currency = $currency;
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
    public function getSum(): float
    {
        return $this->sum;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @inheritDoc
     */
    public function present(): string
    {
        $formattedSum = number_format($this->sum, 2);
        return "{$this->name} - $formattedSum {$this->currency}";
    }
}
