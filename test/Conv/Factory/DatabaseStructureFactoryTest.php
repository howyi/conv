<?php

namespace Conv\Factory;

use Conv\Structure\DatabaseStructure;

class DatabaseStructureFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFromYaml()
    {
        $actual = DatabaseStructureFactory::fromDir('test/schema/');
        // TODO
        $this->assertInstanceOf(DatabaseStructure::class, $actual);
    }
}
