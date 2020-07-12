<?php


namespace App\Cli\Command;

use App\Output\PresentableInterface;

/**
 * Interface CliCommandInterface
 * @package App\Command
 */
interface CliCommandInterface
{
    /**
     * @param array $args
     * @return PresentableInterface[]
     * @throws \Exception
     */
    public function run($args): array;
}
