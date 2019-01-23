<?php

namespace Howyi\Conv\Generator;

use Howyi\Conv\Util\FieldOrder;

class FieldOrderGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider generateProvider
     */
    public function testGenerate($before, $after, $expected)
    {
        $actual = FieldOrderGenerator::generate($before, $after);
        $this->assertEquals($expected, $actual);
    }

    public function generateProvider()
    {
        return [
            [
                ['A', 'B', 'C', 'D', 'E'],
                ['A', 'C', 'D', 'B', 'E'],
                [
                    'B' => new FieldOrder('B', 'A', 'D')
                ],
            ],
            [
                ['A', 'B', 'C', 'D', 'E'],
                ['B', 'C', 'D', 'E', 'A'],
                [
                    'A' => new FieldOrder('A', null, 'E')
                ],
            ],
            [
                ['A', 'B', 'C', 'D', 'E'],
                ['D', 'B', 'C', 'E', 'A'],
                [
                    'A' => new FieldOrder('A', null, 'E'),
                    'D' => new FieldOrder('D', 'C', null),
                ],
            ],
            [
                ['A', 'B', 'C', 'D', 'E'],
                ['A', 'B', 'C', 'D', 'E'],
                [],
            ]
        ];
    }

    /**
     * @dataProvider onesideProvider
     */
    public function testOneside($before, $after, $expected)
    {
        $actual = FieldOrderGenerator::oneside($before, $after);
        $this->assertEquals($expected, $actual);
    }

    public function onesideProvider()
    {
        return [
            [
                [],
                [],
                []
            ],
            [
                ['A'],
                [],
                [
                    ['before' => 'A', 'after' => null],
                ]
            ],
            [
                ['A', 'B', 'C'],
                ['A', 'C'],
                [
                    ['before' => 'A', 'after' => 'A'],
                    ['before' => 'B', 'after' => null],
                    ['before' => 'C', 'after' => 'C'],
                ],
            ],
            [
                ['A', 'B', 'C', 'D'],
                ['B', 'C', 'D'],
                [
                    ['before' => 'A', 'after' => null],
                    ['before' => 'B', 'after' => 'B'],
                    ['before' => 'C', 'after' => 'C'],
                    ['before' => 'D', 'after' => 'D'],
                ],
            ],
            [
                ['C', 'D', 'E'],
                ['C', 'A', 'D'],
                [
                    ['before' => 'C', 'after' => 'C'],
                    ['before' => null, 'after' => 'A'],
                    ['before' => 'D', 'after' => 'D'],
                    ['before' => 'E', 'after' => null],
                ],
            ]
        ];
    }
}
