<?php


namespace App\Output;

/**
 * Class PrinterInterface
 * @package App\Output
 */
interface PrinterInterface
{
    /**
     * @param string $message
     */
    public function printMessage(string $message): void;

    /**
     * @param string $errorMessage
     */
    public function printError(string $errorMessage): void;

    /**
     * @param PresentableInterface $presentable
     */
    public function print(PresentableInterface $presentable): void;
}
