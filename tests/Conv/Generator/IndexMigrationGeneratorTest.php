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
                ['id' => new IndexStructure('id', false, ['id'])],
                ['id' => new IndexStructure('id', false, ['id'])],
                new IndexAllMigrationLine(),
            ],
            [
                $before = ['id' => new IndexStructure('id', false, ['id'])],
                $after = ['id' => new IndexStructure('id', true, ['id'])],
                new IndexAllMigrationLine(
                    new IndexDropMigrationLine($before),
                    new IndexAddMigrationLine($after)
                ),
            ],
            [
                [],
                $after = [
                    'id'  => new IndexStructure('id', true, ['id']),
                    'age' => new IndexStructure('age', false, ['age'])
                ],
                new IndexAllMigrationLine(
                    null,
                    new IndexAddMigrationLine($after)
                ),
            ],
            [
                $before = [
                    'id'  => new IndexStructure('id', true, ['id']),
                    'age' => new IndexStructure('age', false, ['age'])
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
