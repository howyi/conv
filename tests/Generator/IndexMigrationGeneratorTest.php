<?php

namespace Howyi\Conv\Generator;

use Howyi\Conv\Migration\Line\IndexAddMigrationLine;
use Howyi\Conv\Migration\Line\IndexDropMigrationLine;
use Howyi\Conv\Migration\Line\IndexAllMigrationLine;
use Howyi\Conv\Structure\IndexStructure;

class IndexMigrationGeneratorTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('generateProvider')]
    public function testGenerate($before, $after, $expected)
    {
        $actual = IndexMigrationGenerator::generate($before, $after);
        $this->assertEquals($expected, $actual);
    }

    public static function generateProvider()
    {
        return [
            [
                ['id' => new IndexStructure('id', false, 'BTREE', ['id'])],
                ['id' => new IndexStructure('id', false, 'BTREE', ['id'])],
                new IndexAllMigrationLine(),
            ],
            [
                $before = ['id' => new IndexStructure('id', false, 'BTREE', ['id'])],
                $after = ['id' => new IndexStructure('id', true, 'BTREE', ['id'])],
                new IndexAllMigrationLine(
                    new IndexDropMigrationLine($before),
                    new IndexAddMigrationLine($after)
                ),
            ],
            [
                [],
                $after = [
                    'id'  => new IndexStructure('id', true, 'BTREE', ['id']),
                    'age' => new IndexStructure('age', false, 'BTREE', ['age'])
                ],
                new IndexAllMigrationLine(
                    null,
                    new IndexAddMigrationLine($after)
                ),
            ],
            [
                $before = [
                    'id'  => new IndexStructure('id', true, 'BTREE', ['id']),
                    'age' => new IndexStructure('age', false, 'BTREE', ['age'])
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
