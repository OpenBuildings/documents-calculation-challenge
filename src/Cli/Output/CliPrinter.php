<?php


namespace App\Cli\Output;

use App\Output\PresentableInterface;
use App\Output\PrinterInterface;

/**
 * Class CliPrinter
 * @package App\Output
 */
class CliPrinter implements PrinterInterface
{
    /**
     * @inheritDoc
     */
    public function printMessage(string $message): void
    {
        echo $message;
        $this->printNewLine();
    }

    /**
     * @inheritDoc
     */
    public function printError(string $errorMessage): void
    {
        echo "ERROR: ";
        $this->printMessage($errorMessage);
    }

    /**
     * @inheritDoc
     */
    public function print(PresentableInterface $presentable): void
    {
        $this->printMessage($presentable->present());
    }

    private function printNewLine(): void
    {
        echo "\n";
    }
}
