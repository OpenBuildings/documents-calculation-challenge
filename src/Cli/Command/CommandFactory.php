<?php


namespace App\Cli\Command;

use App\Reader\CsvReader;
use App\Service\CalculationService;

/**
 * Class CommandFactory
 * @package App\Command
 */
class CommandFactory implements CommandFactoryInterface
{
    /** @var \Closure[] */
    private $commandsRegistry = [];

    public function __construct()
    {
        $this->commandsRegistry['import'] = function () {
            return new ImportCommand(new CalculationService(), new CsvReader());
        };
    }

    /**
     * @param string $name
     * @return CliCommandInterface
     * @throws \Exception
     */
    public function getCommand(string $name): CliCommandInterface
    {
        if (!array_key_exists($name, $this->commandsRegistry)) {
            throw new \Exception('Unsupported command');
        }

        return $this->commandsRegistry['import']();
    }
}
