<?php

namespace Conv\Factory;

use Conv\Config;
use Conv\Factory\TableStructureFactory;
use Conv\Structure\ColumnStructure;
use Conv\Structure\DatabaseStructure;
use Conv\Structure\IndexStructure;
use Conv\Structure\TableStructureType;
use Conv\Util\Evaluator;
use Conv\Util\SchemaKey;
use Conv\Util\SchemaValidator;
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
        $specList = [];
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $name = pathinfo($fileinfo->getPathName(), PATHINFO_FILENAME);
                switch (strtolower($fileinfo->getExtension())) {
                    case 'yml':
                    case 'yaml':
                        // エラー制御演算子によって表示されないキー重複エラーを出力させる
                        set_error_handler(
                            function ($errno, $errstr, $errfile, $errline) {
                                throw new \ErrorException(
                                    $errstr,
                                    0,
                                    $errno,
                                    $errfile,
                                    $errline
                                );
                            },
                            E_USER_DEPRECATED
                        );
                        $specList[$name] = Yaml::parse(file_get_contents($fileinfo->getPathName()));
                        restore_error_handler();
                        break;
                    default:
                        break;
                }
            }
        }

        foreach ($specList as $name => $spec) {
            if (Config::option('eval')) {
                $spec = Evaluator::evaluate($spec);
            }
            if (!isset($spec[SchemaKey::TABLE_TYPE])) {
                $spec[SchemaKey::TABLE_TYPE] = TableStructureType::TABLE;
            }
            SchemaValidator::validate($name, $spec);
            switch ($spec[SchemaKey::TABLE_TYPE]) {
                case TableStructureType::TABLE:
                    $table = TableStructureFactory::fromSpec($name, $spec);
                    $tableList[$table->getTableName()] = $table;
                    break;
                case TableStructureType::VIEW:
                    // TODO
                    break;
            }
        }
        // dump($specList);
        return new DatabaseStructure($tableList);
    }

    /**
     * @param \PDO          $pdo
     * @param string        $dbName
     * @param callable|null $filter
     */
    public static function fromPDO(
        \PDO $pdo,
        string $dbName,
        callable $filter = null
    ): DatabaseStructure {
        $rawTableList = $pdo->query(
            "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = '$dbName'"
        )->fetchAll();
        $tableList = [];
        foreach ($rawTableList as $value) {
            $tableName = $value['TABLE_NAME'];
            switch ($value['TABLE_TYPE']) {
                case 'BASE TABLE':
                    $table = TableStructureFactory::fromTable($pdo, $dbName, $tableName);
                    break;
                case 'VIEW':
                    // TODO
                    continue 2;
                default:
                    continue 2;
            }
            if (!is_null($filter) and !$filter($table)) {
                continue;
            }
            $tableList[$tableName] = $table;
        }
        return new DatabaseStructure($tableList);
    }
}
