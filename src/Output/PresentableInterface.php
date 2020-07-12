<?php


namespace App\Output;

/**
 * Interface PresentableInterface
 * @package App\Output
 */
interface PresentableInterface
{
    /**
     * @return string
     */
    public function present(): string;
}
