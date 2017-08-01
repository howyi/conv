<?php

namespace Conv\Generator;

use Conv\Migration\Line\IndexAddMigrationLine;
use Conv\Migration\Line\IndexDropMigrationLine;
use Conv\Migration\Line\IndexAllMigrationLine;
use Conv\Structure\IndexStructure;

class IndexMigrationGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider generateProvider
     */
    public function testGenerate($before, $after, $expected)
    {
        $actual = IndexMigrationGenerator::generate($before, $after);
        $this->assertEquals($expected, $actual);
    }

    public function generateProvider()
    {
        return [
            [
                [new IndexStructure('id', ['id'], false)],
                [new IndexStructure('id', ['id'], false)],
                new IndexAllMigrationLine(),
            ],
            [
                $a = [new IndexStructure('id', ['id'], false)],
                $b = [new IndexStructure('id', ['id'], true)],
                new IndexAllMigrationLine(
                    new IndexDropMigrationLine($a),
                    new IndexAddMigrationLine($b)
                ),
            ],
            [
                $a = [],
                $b = [
                    new IndexStructure('id', ['id'], true),
                    new IndexStructure('age', ['age'], false)
                ],
                new IndexAllMigrationLine(
                    null,
                    new IndexAddMigrationLine($b)
                ),
            ]
        ];
    }
}
