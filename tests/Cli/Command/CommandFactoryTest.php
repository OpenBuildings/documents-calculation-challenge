<?php


namespace App\Test\Cli\Command;

use App\Cli\Command\CommandFactory;
use App\Cli\Command\ImportCommand;
use PHPUnit\Framework\TestCase;

/**
 * Class CommandFactoryTest
 * @package Command
 */
class CommandFactoryTest extends TestCase
{
    /** @var CommandFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new CommandFactory();
    }

    public function testGetImportCommand()
    {
        $result = $this->factory->getCommand('import');

        $this->assertInstanceOf(ImportCommand::class, $result);
    }

    public function testGetCommandThrowsExceptionWhenCommandIsNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported command');

        $this->factory->getCommand('invalid');
    }
}
