<?php

namespace Howyi\Conv;

use Composer\Semver\Semver;
use Howyi\Conv\Operator\DropOnlySilentOperator;
use Howyi\Conv\Structure\DatabaseStructure;

class MigrationGeneratorSingleTest extends \PHPUnit\Framework\TestCase
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
     * @param string $name
     * @param string $beforeDir
     * @param string $afterDir
     * @param string $upPath
     * @param string $downPath
     * @param \PDO   $pdo
     */
    public function testGenerate($name, $beforeDir, $afterDir, $upPath, $downPath, $pdo)
    {
        $mysqlVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
        preg_match("/^[0-9\.]+/", $mysqlVersion, $match);
        $mysqlVersion = $match[0];

        $exploded = explode(':', $name);
        $target = $exploded[1] ?? null;

        if (!is_null($target)) {
            if (!Semver::satisfies($mysqlVersion, $target)) {
                // Skipped
                $this->assertTrue(true);
                return;
            }
        }
        if (file_exists($afterDir)) {
            $after = DatabaseStructureFactory::fromSqlDir(
                $pdo,
                $afterDir,
                new DropOnlySilentOperator()
            );
        } else {
            $after = new DatabaseStructure([]);
        }

        if (file_exists($beforeDir)) {
            $before = DatabaseStructureFactory::fromSqlDir(
                $pdo,
                $beforeDir,
                new DropOnlySilentOperator(),
                null,
                false
            );
        } else {
            $pdo->exec('DROP DATABASE IF EXISTS ' . DatabaseStructureFactory::TMP_DBNAME);
            $pdo->exec('CREATE DATABASE ' . DatabaseStructureFactory::TMP_DBNAME);
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

        $pdo->exec('USE ' . DatabaseStructureFactory::TMP_DBNAME);

        $pdo->exec(stripslashes($migration->getUp()));
        $this->assertEquals(
            $after,
            DatabaseStructureFactory::fromPDO($pdo, DatabaseStructureFactory::TMP_DBNAME)
        );

        $pdo->exec(stripslashes($migration->getDown()));
        $this->assertEquals(
            $before,
            DatabaseStructureFactory::fromPDO($pdo, DatabaseStructureFactory::TMP_DBNAME)
        );

        $pdo->exec(stripslashes($migration->getUp()));
        $this->assertEquals(
            $after,
            DatabaseStructureFactory::fromPDO($pdo, DatabaseStructureFactory::TMP_DBNAME)
        );
    }

    public function generateProvider()
    {
        $dir = 'vendor/laminaria/conv-test-suite/cases/part/';

        foreach (TestUtility::getPdoArray() as $pdo) {
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
                    $pdo,
                ];
            }
        }
    }
}
