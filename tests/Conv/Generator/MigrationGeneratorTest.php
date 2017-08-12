<?php

namespace Conv\Generator;

use Conv\Factory\DatabaseStructureFactory;
use Conv\Factory\TableStructureFactory;
use Conv\Migration\Table\TableAlterMigration;
use Conv\Migration\Table\TableCreateMigration;
use Conv\Migration\Table\TableDropMigration;
use Conv\Structure\DatabaseStructure;
use Conv\Structure\TableStructure;
use Conv\Util\Operator;
use Prophecy\Argument as arg;
use Symfony\Component\Yaml\Yaml;

class MigrationGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private $prophet;

    protected function setup()
    {
        $this->prophet = new \Prophecy\Prophet;
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
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
                DatabaseStructureFactory::fromDir('tests/schema/'),
                new DatabaseStructure([]),
                [
                    TableDropMigration::class,
                    TableDropMigration::class,
                ]
            ],
            [
                new DatabaseStructure([]),
                DatabaseStructureFactory::fromDir('tests/schema/'),
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
                Yaml::parse(file_get_contents('tests/schema/tbl_user.yml'))
            ),
            'tbl_music' => TableStructureFactory::fromSpec(
                'tbl_music',
                Yaml::parse(file_get_contents('tests/schema/tbl_music.yml'))
            ),
        ]);
        $after = new DatabaseStructure([
            TableStructureFactory::fromSpec(
                'tbl_user',
                Yaml::parse(file_get_contents('tests/schema/tbl_user.yml'))
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
                Yaml::parse(file_get_contents('tests/schema/tbl_user.yml'))
            ),
        ]);
        $after = new DatabaseStructure([
            'tbl_music' => TableStructureFactory::fromSpec(
                'tbl_music',
                Yaml::parse(file_get_contents('tests/schema/tbl_music.yml'))
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
                Yaml::parse(file_get_contents('tests/schema/tbl_user.yml'))
            ),
        ]);
        $after = new DatabaseStructure([
            'tbl_music' => TableStructureFactory::fromSpec(
                'tbl_music',
                Yaml::parse(file_get_contents('tests/schema/tbl_music.yml'))
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
