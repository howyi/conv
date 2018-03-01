<?php

namespace Conv;

use Conv\Util\Config;
use Conv\Factory\TableStructureFactory;
use Conv\Factory\ViewStructureFactory;
use Conv\Structure\ColumnStructure;
use Conv\Structure\DatabaseStructure;
use Conv\Structure\IndexStructure;
use Conv\Structure\TableStructureType;
use Conv\Util\Evaluator;
use Conv\Util\SchemaKey;
use Conv\Util\SchemaValidator;
use Howyi\Evi;

class DatabaseStructureFactory
{
	private const TMP_DBNAME = 'conv_tmp';

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
                $specList[$name] = Evi::parse($fileinfo->getPathName(), Config::option('eval'));
            }
        }

        foreach ($specList as $name => $spec) {
            if (!isset($spec[SchemaKey::TABLE_TYPE])) {
                $spec[SchemaKey::TABLE_TYPE] = TableStructureType::TABLE;
            }
            SchemaValidator::validate($name, $spec);
            if ($spec[SchemaKey::TABLE_TYPE] === TableStructureType::TABLE) {
                $table = TableStructureFactory::fromSpec($name, $spec);
            } else {
                $table = ViewStructureFactory::fromSpec($name, $spec);
            }
            $tableList[$table->getName()] = $table;
        }
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
                    $table = ViewStructureFactory::fromView($pdo, $dbName, $tableName);
                    break;
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

	/**
	 * @param \PDO          $pdo      Creatable DB
	 * @param string        $path
	 * @param Operator      $operator
	 * @param callable|null $filter
	 * @return DatabaseStructure
	 */
	public static function fromSqlDir(
		\PDO $pdo,
		string $path,
		Operator $operator,
		callable $filter = null
	): DatabaseStructure {
		$operator->output('<comment>Genarate temporary database</>');
		$pdo->exec('DROP DATABASE IF EXISTS ' . self::TMP_DBNAME);
		$pdo->exec('CREATE DATABASE ' . self::TMP_DBNAME);
		$pdo->exec('USE ' . self::TMP_DBNAME);
		$viewQueryList = [];
		$progress = $operator->getProgress(count(glob("$path/*.sql")));
		$progress->start();
		foreach (new \DirectoryIterator($path) as $fileInfo) {
			if (!$fileInfo->isFile()) {
				continue;
			}
			if ('sql' !== strtolower($fileInfo->getExtension())) {
				continue;
			}
			$query = file_get_contents($fileInfo->getRealPath());

			if (false === strpos($query, 'CREATE ALGORITHM')) {
				$pdo->exec($query);
				$progress->advance();
			} else {
				$viewQueryList[] = $query;
			}
		}
		foreach ($viewQueryList as $query) {
			$pdo->exec($query);
			$progress->advance();
		}
		$progress->finish();
		$databaseStructure = self::fromPDO($pdo, self::TMP_DBNAME, $filter);
		$pdo->exec('DROP DATABASE IF EXISTS ' . self::TMP_DBNAME);

		return $databaseStructure;
	}
}
