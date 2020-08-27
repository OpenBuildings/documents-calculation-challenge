<?php

use App\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $config = new Config();
        $this->assertInstanceOf(Config::class, $config);
    }

    public function testMagicGet()
    {
        $config = $this->createMock(Config::class);
        $config->expects($this->once())
            ->method('__get')
            ->with($this->equalTo('supported_currencies'))
            ->will($this->returnValue([
                'EUR',
                'USD',
                'GBP',
            ]));

        $this->assertIsArray($config->supported_currencies);
    }
}
