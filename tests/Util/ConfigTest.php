<?php

namespace Laminaria\Conv\Util;

use Symfony\Component\Yaml\Yaml;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $c = new Config();
        $this->assertSame($c::DEFAULT['default']['engine'], $c::default('engine'));
        $this->assertSame($c::DEFAULT['default']['engine'], $c::default('engine'));
        $this->assertSame($c::DEFAULT['option']['eval'], $c::option('eval'));
    }

    public function testConstructNoConfigFile()
    {
        unlink('conv.yml');
        $c = new Config();
        $this->assertSame($c::DEFAULT['default']['engine'], $c::default('engine'));
        $config = $c::DEFAULT;
        unset($config['option']);
        file_put_contents('conv.yml', Yaml::dump($config));
    }
}
