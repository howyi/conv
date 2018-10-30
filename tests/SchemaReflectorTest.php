<?php

namespace Laminaria\Conv;

use Laminaria\Conv\Util\Config;
use Laminaria\Conv\DatabaseStructureFactory;
use Laminaria\Conv\Factory\TableStructureFactory;
use Laminaria\Conv\Migration\Table\TableAlterMigration;
use Laminaria\Conv\Migration\Table\TableCreateMigration;
use Laminaria\Conv\Migration\Table\TableDropMigration;
use Laminaria\Conv\Migration\Table\ViewDropMigration;
use Laminaria\Conv\Migration\Table\ViewAlterMigration;
use Laminaria\Conv\Migration\Table\ViewCreateMigration;
use Laminaria\Conv\Migration\Table\ViewRenameMigration;
use Laminaria\Conv\Structure\DatabaseStructure;
use Laminaria\Conv\Structure\TableStructure;
use Laminaria\Conv\Operator;
use Howyi\Evi;
use Prophecy\Argument as arg;
use Laminaria\Conv\SchemaReflector;
use Symfony\Component\Console\Helper\ProgressBar;

class SchemaReflectorTest extends \PHPUnit\Framework\TestCase
{
    private $pdo;
    private $prophet;

    protected function setup()
    {
        $this->pdo = new \PDO("mysql:host=127.0.0.1;dbname=conv_test;charset=utf8;", 'root', '');
        $this->prophet = new \Prophecy\Prophet;
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    public function testFromDatabaseStructure()
    {
        $structure = DatabaseStructureFactory::fromPDO(
            $this->pdo,
            'conv_test'
        );
        SchemaReflector::fromDatabaseStructure('build/schema', $structure);
        SchemaReflector::fromDatabaseStructure('build/schema', $structure);
        $this->assertTrue(true);
    }
}
