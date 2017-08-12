<?php

namespace Conv\Factory;

use Conv\Structure\TableStructure;
use Symfony\Component\Yaml\Yaml;

class TableStructureFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFromSpec()
    {
        $spec = Yaml::parse(file_get_contents('tests/schema/tbl_user.yml'));
        $actual = TableStructureFactory::fromSpec('tbl_user', $spec);
        // TODO
        $this->assertInstanceOf(TableStructure::class, $actual);
    }
}
