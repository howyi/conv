<?php

namespace Conv;

use Conv\Util\Config;
use Conv\DatabaseStructureFactory;
use Conv\Factory\TableStructureFactory;
use Conv\Migration\Table\TableAlterMigration;
use Conv\Migration\Table\TableCreateMigration;
use Conv\Migration\Table\TableDropMigration;
use Conv\Migration\Table\ViewDropMigration;
use Conv\Migration\Table\ViewAlterMigration;
use Conv\Migration\Table\ViewCreateMigration;
use Conv\Structure\DatabaseStructure;
use Conv\Structure\TableStructure;
use Conv\Operator;
use Howyi\Evi;
use Prophecy\Argument as arg;

class MigrationGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private $pdo;
    private $prophet;

    protected function setup()
    {
        $this->pdo = new \PDO("mysql:host=localhost;dbname=conv_test;charset=utf8;", 'root', '');
        $this->prophet = new \Prophecy\Prophet;
    }

    protected function tearDown()
    {
        $structure = DatabaseStructureFactory::fromPDO($this->pdo, 'conv_test');
        $this->prophet->checkPredictions();
    }

    /**
     * @dataProvider generateProvider
     */
    public function testGenerate($dir, $calls, $expected)
    {
        $expectStructure = DatabaseStructureFactory::fromDir($dir);
        $actualStructure = DatabaseStructureFactory::fromPDO($this->pdo, 'conv_test');
        $operator = $this->prophet->prophesize(Operator::class);

        foreach ($calls as $value) {
            $operator->choiceQuestion(
                $value['message'],
                $value['choices']
            )->willReturn($value['return'])
            ->shouldBeCalledTimes(1);
        }

        $alter = MigrationGenerator::generate(
            $actualStructure,
            $expectStructure,
            $operator->reveal()
        );
        for ($i = 0; $i < count($alter->getMigrationList()); $i++) {
            $this->assertInstanceOf($expected[$i], $alter->getMigrationList()[$i]);
        }
        foreach ($alter->getMigrationList() as $migration) {
            $this->pdo->exec($migration->getUp());
        }
        foreach ($alter->getMigrationList() as $migration) {
            $this->pdo->exec($migration->getDown());
        }
        foreach ($alter->getMigrationList() as $migration) {
            $this->pdo->exec($migration->getUp());
        }
    }

    public function generateProvider()
    {
        return [
            [
                'tests/Retort/test_schema/000',
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
                'tests/Retort/test_schema/001',
                [],
                [
                    TableAlterMigration::class,
                ]
            ],
            [
                'tests/Retort/test_schema/002',
                [],
                [
                    TableDropMigration::class,
                    ViewDropMigration::class,
                ]
            ],
            [
                'tests/Retort/test_schema/003',
                [],
                [
                    ViewAlterMigration::class,
                ]
            ],
        ];
    }

    /**
     * @dataProvider generateWhenNoQuestionProvider
     */
    public function testGenerateWhenNoQuestion($before, $after, $expected)
    {
        $operator = $this->prophet->prophesize(Operator::class);
        $migration = MigrationGenerator::generate($before, $after, $operator->reveal());
        for ($i = 0; $i < count($migration->getMigrationList()); $i++) {
            $this->assertInstanceOf($expected[$i], $migration->getMigrationList()[$i]);
        }
    }

    public function generateWhenNoQuestionProvider()
    {
        return [
            [
                DatabaseStructureFactory::fromDir('tests/Retort/test_schema/draft/'),
                new DatabaseStructure([]),
                [
                    TableDropMigration::class,
                    TableDropMigration::class,
                ]
            ],
            [
                new DatabaseStructure([]),
                DatabaseStructureFactory::fromDir('tests/Retort/test_schema/draft/'),
                [
                    TableCreateMigration::class,
                    TableCreateMigration::class,
                ]
            ],
        ];
    }

    public function testGenerateWhenNotModify()
    {
        $before = new DatabaseStructure([
            'tbl_user'  => TableStructureFactory::fromSpec(
                'tbl_user',
                Evi::parse('tests/Retort/test_schema/draft/tbl_user.yml', Config::option('eval'))
            ),
            'tbl_music' => TableStructureFactory::fromSpec(
                'tbl_music',
                Evi::parse('tests/Retort/test_schema/draft/tbl_music.yml', Config::option('eval'))
            ),
        ]);
        $after = new DatabaseStructure([
            TableStructureFactory::fromSpec(
                'tbl_user',
                Evi::parse('tests/Retort/test_schema/draft/tbl_user.yml', Config::option('eval'))
            ),
        ]);
        $operator = $this->prophet->prophesize(Operator::class);
        $migration = MigrationGenerator::generate($before, $after, $operator->reveal());
        $this->assertInstanceOf(TableDropMigration::class, $migration->getMigrationList()[0]);
    }

    public function testGenerateWhenDropAndCreate()
    {
        $before = new DatabaseStructure([
            'tbl_user' => TableStructureFactory::fromSpec(
                'tbl_user',
                Evi::parse('tests/Retort/test_schema/draft/tbl_user.yml', Config::option('eval'))
            ),
        ]);
        $after = new DatabaseStructure([
            'tbl_music' => TableStructureFactory::fromSpec(
                'tbl_music',
                Evi::parse('tests/Retort/test_schema/draft/tbl_music.yml', Config::option('eval'))
            ),
        ]);
        $operator = $this->prophet->prophesize(Operator::class);
        $operator->choiceQuestion(
            \Prophecy\Argument::any(),
            \Prophecy\Argument::any()
        )->willReturn('dropped')
        ->shouldBeCalledTimes(1);
        $migration = MigrationGenerator::generate($before, $after, $operator->reveal());
        $this->assertInstanceOf(TableDropMigration::class, $migration->getMigrationList()[0]);
        $this->assertInstanceOf(TableCreateMigration::class, $migration->getMigrationList()[1]);
    }

    public function testGenerateWhenRename()
    {
        $before = new DatabaseStructure([
            'tbl_user' => TableStructureFactory::fromSpec(
                'tbl_user',
                Evi::parse('tests/Retort/test_schema/draft/tbl_user.yml', Config::option('eval'))
            ),
        ]);
        $after = new DatabaseStructure([
            'tbl_music' => TableStructureFactory::fromSpec(
                'tbl_music',
                Evi::parse('tests/Retort/test_schema/draft/tbl_music.yml', Config::option('eval'))
            ),
        ]);
        $operator = $this->prophet->prophesize(Operator::class);
        $operator->choiceQuestion(
            'Table tbl_user is missing. Choose an action.',
            ['dropped', 'renamed (tbl_music)']
        )->willReturn('renamed (tbl_music)')
        ->shouldBeCalledTimes(1);
        $operator->choiceQuestion(
            \Prophecy\Argument::any(),
            \Prophecy\Argument::any()
        )->willReturn('dropped')
        ->shouldBeCalledTimes(3);
        $operator->output(\Prophecy\Argument::any())->shouldBeCalledTimes(7);
        $migration = MigrationGenerator::generate($before, $after, $operator->reveal());
        $this->assertInstanceOf(TableAlterMigration::class, $migration->getMigrationList()[0]);
    }
}
