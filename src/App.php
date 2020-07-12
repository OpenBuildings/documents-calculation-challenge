<?php


namespace App;

use App\Cli\Command\CommandFactoryInterface;
use App\Output\PrinterInterface;

/**
 * Class App
 * @package App
 */
class App
{
    /** @var CommandFactoryInterface */
    private $commandFactory;
    /** @var PrinterInterface */
    private $printer;

    public function __construct(CommandFactoryInterface $commandFactory, PrinterInterface $printer)
    {
        $this->commandFactory = $commandFactory;
        $this->printer = $printer;
    }

    /**
     * @param string $commandName
     * @param array $args
     */
    public function runCommand($commandName, $args): void
    {
        try {
            $result = $this->commandFactory->getCommand($commandName)->run($args);
            foreach ($result as $line) {
                $this->printer->print($line);
            }
        } catch (\Exception $e) {
            $this->printer->printError($e->getMessage());
        }
    }
}
