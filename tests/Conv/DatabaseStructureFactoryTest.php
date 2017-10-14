<?php

namespace Conv;

use Conv\Structure\DatabaseStructure;

class DatabaseStructureFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFromYaml()
    {
        $actual = DatabaseStructureFactory::fromDir('tests/Retort/test_schema/');
        // TODO
        $this->assertInstanceOf(DatabaseStructure::class, $actual);
    }
}
