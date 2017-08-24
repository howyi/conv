<?php

namespace Conv\Factory;

use Conv\Config;
use Conv\Structure\TableStructure;
use Howyi\Evi;

class TableStructureFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testFromSpec()
    {
        $spec = Evi::parse('tests/schema/tbl_user.yml', Config::option('eval'), '$ref', '$ext');
        $actual = TableStructureFactory::fromSpec('tbl_user', $spec);
        // TODO
        $this->assertInstanceOf(TableStructure::class, $actual);
    }
}
