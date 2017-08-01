<?php

namespace Conv\Factory;

use Conv\Structure\ColumnStructure;
use Conv\Structure\IndexStructure;
use Conv\Structure\DatabaseStructure;
use Conv\Factory\TableStructureFactory;
use Symfony\Component\Yaml\Yaml;

class DatabaseStructureFactory
{
    /**
     * @param string $path
     */
    public static function fromDir(
        string $path
    ): DatabaseStructure {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );
        $tableList = [];
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                switch (strtolower($fileinfo->getExtension())) {
                    case 'yml':
                    case 'yaml':
                        $table = TableStructureFactory::fromYaml($fileinfo->getPathName());
                        $tableList[$table->getTableName()] = $table;
                        break;
                    default:
                        break;
                }
            }
        }
        return new DatabaseStructure($tableList);
    }

    /**
     * @param \PDO $pdo
     */
    public static function fromPDO(
        \PDO $pdo,
        callable $filter = null
    ): DatabaseStructure {
        $rawTableList = $pdo->query("SHOW TABLES")->fetchAll();
        $tableList = [];
        foreach ($rawTableList as $value) {
            $tableName = $value[0];
            $table = TableStructureFactory::fromTable($pdo, $tableName);
            if (!is_null($filter) and !$filter($table)) {
                continue;
            }
            $tableList[$tableName] = $table;
        }
        return new DatabaseStructure($tableList);
    }
}
