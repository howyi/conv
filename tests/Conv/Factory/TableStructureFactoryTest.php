<?php

namespace Conv\Factory;

use Conv\Util\Config;
use Conv\Structure\TableStructure;
use Howyi\Evi;

class TableStructureFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFromSpec()
    {
        $spec = Evi::parse('tests/Retort/test_schema/tbl_user.yml', Config::option('eval'));
        $actual = TableStructureFactory::fromSpec('tbl_user', $spec);
        // TODO
        $this->assertInstanceOf(TableStructure::class, $actual);
    }
}
