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
use Conv\Migration\Table\ViewRenameMigration;
use Conv\Structure\DatabaseStructure;
use Conv\Structure\TableStructure;
use Conv\Operator;
use Howyi\Evi;
use Prophecy\Argument as arg;
use Conv\SchemaReflector;
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
