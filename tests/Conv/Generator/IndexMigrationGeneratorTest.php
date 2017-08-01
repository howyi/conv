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
                ['id' => new IndexStructure('id', ['id'], false)],
                ['id' => new IndexStructure('id', ['id'], false)],
                new IndexAllMigrationLine(),
            ],
            [
                $before = ['id' => new IndexStructure('id', ['id'], false)],
                $after = ['id' => new IndexStructure('id', ['id'], true)],
                new IndexAllMigrationLine(
                    new IndexDropMigrationLine($before),
                    new IndexAddMigrationLine($after)
                ),
            ],
            [
                [],
                $after = [
                    'id'  => new IndexStructure('id', ['id'], true),
                    'age' => new IndexStructure('age', ['age'], false)
                ],
                new IndexAllMigrationLine(
                    null,
                    new IndexAddMigrationLine($after)
                ),
            ],
            [
                $before = [
                    'id'  => new IndexStructure('id', ['id'], true),
                    'age' => new IndexStructure('age', ['age'], false)
                ],
                [],
                new IndexAllMigrationLine(
                    new IndexDropMigrationLine($before),
                    null
                ),
            ]
        ];
    }
}
