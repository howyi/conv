<?php

namespace Conv;

use Conv\Structure\DatabaseStructure;

class DatabaseStructureFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFromYaml()
    {
        $actual = DatabaseStructureFactory::fromDir('tests/schema/');
        // TODO
        $this->assertInstanceOf(DatabaseStructure::class, $actual);
    }
}
