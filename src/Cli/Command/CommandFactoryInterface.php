<?php


namespace App\Cli\Command;

/**
 * Class CommandFactoryInterface
 * @package App\Command
 */
interface CommandFactoryInterface
{
    /**
     * @param string $name
     * @return CliCommandInterface
     * @throws \Exception
     */
    public function getCommand(string $name): CliCommandInterface;
}
