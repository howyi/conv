<?php

namespace Howyi\Conv;

use Howyi\Conv\Migration\Table\TableAlterMigration;
use Howyi\Conv\Migration\Table\TableCreateMigration;
use Howyi\Conv\Migration\Table\TableDropMigration;
use Howyi\Conv\Migration\Table\ViewAlterMigration;
use Howyi\Conv\Migration\Table\ViewCreateMigration;
use Howyi\Conv\Migration\Table\ViewDropMigration;
use Howyi\Conv\Migration\Table\ViewRenameMigration;
use Howyi\Conv\Operator\ConsoleOperator;
use Prophecy\Argument as arg;

class MigrationGeneratorMultiTest extends \PHPUnit\Framework\TestCase
{
    private $prophet;

    protected function setup()
    {
        $this->prophet = new \Prophecy\Prophet();
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    /**
     * @dataProvider generateProvider
     * @param string $dir
     * @param string $calls
     * @param string $expected
     */
    public function testGenerate($dir, $calls, $expected, $pdo)
    {
            $operator = $this->prophet->prophesize(ConsoleOperator::class);
            $expectStructure = DatabaseStructureFactory::fromSqlDir(
                $pdo,
                $dir,
                new Operator\DropOnlySilentOperator()
            );
            $pdo->exec('USE conv_test');
            $actualStructure = DatabaseStructureFactory::fromPDO($pdo, 'conv_test');

        foreach ($calls as $value) {
            $operator->choiceQuestion(
                $value['message'],
                arg::type('array')
            )->willReturn($value['return'])
                ->shouldBeCalledTimes(1);
        }
            $operator->output(\Prophecy\Argument::any());

            $alter = MigrationGenerator::generate(
                $actualStructure,
                $expectStructure,
                $operator->reveal()
            );
        foreach ($alter->getMigrationList() as $migration) {
            $pdo->exec($migration->getUp());
        }
        foreach ($alter->getMigrationList() as $migration) {
            $pdo->exec($migration->getDown());
        }
        foreach ($alter->getMigrationList() as $migration) {
            $pdo->exec($migration->getUp());
        }
        for ($i = 0; $i < count($alter->getMigrationList()); $i++) {
            $this->assertInstanceOf($expected[$i], $alter->getMigrationList()[$i]);
        }
    }

    public function generateProvider()
    {
        $values = [
            [
                'vendor/howyi/conv-test-suite/cases/unit/000',
                [],
                [
                    TableCreateMigration::class,
                    TableCreateMigration::class,
                    TableCreateMigration::class,
                    TableCreateMigration::class,
                    TableCreateMigration::class,
                    TableCreateMigration::class,
                    TableCreateMigration::class,
                    ViewCreateMigration::class,
                    ViewCreateMigration::class,
                ]
            ],
            [
                'vendor/howyi/conv-test-suite/cases/unit/001',
                [],
                [
                    TableAlterMigration::class,
                ]
            ],
            [
                'vendor/howyi/conv-test-suite/cases/unit/002',
                [],
                [
                    TableDropMigration::class,
                    ViewDropMigration::class,
                ]
            ],
            [
                'vendor/howyi/conv-test-suite/cases/unit/003',
                [],
                [
                    ViewAlterMigration::class,
                ]
            ],
            [
                'vendor/howyi/conv-test-suite/cases/unit/004',
                [
                    [
                        'message' => 'Table tbl_country is missing. Choose an action.',
                        'return'  => 'renamed (tbl_country2, tbl_country3)',
                    ],
                    [
                        'message' => 'Select a renamed table.',
                        'return'  => 'tbl_country2',
                    ]
                ],
                [
                    TableAlterMigration::class,
                    ViewAlterMigration::class,
                    TableCreateMigration::class,
                ]
            ],
            [
                'vendor/howyi/conv-test-suite/cases/unit/005',
                [
                    [
                        'message' => 'View view_user2 is missing. Choose an action.',
                        'return'  => 'renamed (view_user, view_user3)',
                    ],
                    [
                        'message' => 'Select a renamed view.',
                        'return'  => 'view_user',
                    ]
                ],
                [
                    ViewRenameMigration::class,
                    ViewCreateMigration::class,
                ]
            ],
            [
                'vendor/howyi/conv-test-suite/cases/unit/006',
                [
                    [
                        'message' => 'Column tbl_music.name is missing. Choose an action.',
                        'return'  => 'renamed (music_name, description)',
                    ],
                    [
                        'message' => 'Select a renamed column.',
                        'return'  => 'music_name',
                    ]
                ],
                [
                    TableAlterMigration::class,
                ]
            ],
            [
                'vendor/howyi/conv-test-suite/cases/unit/007',
                [],
                [
                    TableAlterMigration::class,
                ]
            ],
            [
                'vendor/howyi/conv-test-suite/cases/unit/008',
                [],
                [
                    TableAlterMigration::class,
                ]
            ],
        ];

        foreach (TestUtility::getPdoArray() as $pdo) {
            foreach ($values as $value) {
                yield array_merge($value, [$pdo]);
            }
        }
    }
}
