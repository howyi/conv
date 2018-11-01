<?php

namespace Laminaria\Conv;

use Laminaria\Conv\Operator\DropOnlySilentOperator;
use Laminaria\Conv\Structure\DatabaseStructure;

class MigrationGeneratorSingleTest extends \PHPUnit\Framework\TestCase
{
    private $pdo;
    private $prophet;

    protected function setup()
    {
        $this->pdo = new \PDO(
            "mysql:host=127.0.0.1;dbname=conv_test;charset=utf8;",
            'root',
            '',
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
        $this->prophet = new \Prophecy\Prophet();
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    /**
     * @dataProvider generateProvider
     * @param $name
     * @param $beforeDir
     * @param $afterDir
     * @param $upPath
     * @param $downPath
     */
    public function testGenerate($name, $beforeDir, $afterDir, $upPath, $downPath)
    {
        if (file_exists($afterDir)) {
            $after = DatabaseStructureFactory::fromSqlDir(
                $this->pdo,
                $afterDir,
                new DropOnlySilentOperator()
            );
        } else {
            $after = new DatabaseStructure([]);
        }

        if (file_exists($beforeDir)) {
            $before = DatabaseStructureFactory::fromSqlDir(
                $this->pdo,
                $beforeDir,
                new DropOnlySilentOperator(),
                null,
                false
            );
        } else {
            $this->pdo->exec('DROP DATABASE IF EXISTS ' . DatabaseStructureFactory::TMP_DBNAME);
            $this->pdo->exec('CREATE DATABASE ' . DatabaseStructureFactory::TMP_DBNAME);
            $before = new DatabaseStructure([]);
        }

        $migration = MigrationGenerator::generate(
            $before,
            $after,
            new DropOnlySilentOperator()
        )->getMigrationList()[0];

        $expectedUp = file_get_contents($upPath);
        $this->assertSame($expectedUp, $migration->getUp());
        $expectedDown = file_get_contents($downPath);
        $this->assertSame($expectedDown, $migration->getDown());

        $this->pdo->exec('USE ' . DatabaseStructureFactory::TMP_DBNAME);

        $this->pdo->exec(stripslashes($migration->getUp()));
        $this->assertEquals(
            $after,
            DatabaseStructureFactory::fromPDO($this->pdo, DatabaseStructureFactory::TMP_DBNAME)
        );

        $this->pdo->exec(stripslashes($migration->getDown()));
        $this->assertEquals(
            $before,
            DatabaseStructureFactory::fromPDO($this->pdo, DatabaseStructureFactory::TMP_DBNAME)
        );

        $this->pdo->exec(stripslashes($migration->getUp()));
        $this->assertEquals(
            $after,
            DatabaseStructureFactory::fromPDO($this->pdo, DatabaseStructureFactory::TMP_DBNAME)
        );
    }

    public function generateProvider()
    {
        $dir = 'vendor/laminaria/conv-test-suite/cases/part/';

        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            if (!$fileInfo->isDir()) {
                continue;
            }
            $name = $fileInfo->getFilename();

            yield [
                $name,
                $dir . $name . '/before',
                $dir . $name . '/after',
                $dir . $name . '/up.sql',
                $dir . $name . '/down.sql',
            ];
        }
    }
}
