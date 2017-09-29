<?php

namespace Conv\Factory;

use Conv\Structure\ColumnStructure;
use Conv\Structure\IndexStructure;
use Conv\Structure\Attribute;
use Conv\Structure\TableStructure;
use Symfony\Component\Yaml\Yaml;
use Conv\Util\SchemaKey;
use Conv\Util\Evaluator;
use Conv\Util\Config;
use Conv\Util\SchemaValidator;

class TableStructureFactory
{
    /**
     * @param string $tableName
     * @param array  $spec
     * @return TableStructure
     */
    public static function fromSpec(string $tableName, array $spec): TableStructure
    {
        $columnStructureList = [];
        foreach ($spec[SchemaKey::TABLE_COLUMN] as $field => $column) {
            $properties = array_diff_key($column, array_flip(SchemaKey::COLUMN_KEYS));
            $columnStructureList[] = new ColumnStructure(
                $field,
                $column[SchemaKey::COLUMN_TYPE],
                array_key_exists(SchemaKey::COLUMN_DEFAULT, $column) ? $column[SchemaKey::COLUMN_DEFAULT] : null,
                $column[SchemaKey::COLUMN_COMMENT],
                array_key_exists(SchemaKey::COLUMN_ATTRIBUTE, $column) ? $column[SchemaKey::COLUMN_ATTRIBUTE] : [],
                $properties
            );
        }

        $indexStructureList = [];
        if (true === array_key_exists(SchemaKey::TABLE_PRIMARY_KEY, $spec)) {
            $indexStructureList[] = new IndexStructure(
                'PRIMARY',
                true,
                $spec[SchemaKey::TABLE_PRIMARY_KEY]
            );
        }

        if (true === array_key_exists(SchemaKey::TABLE_INDEX, $spec)) {
            foreach ($spec[SchemaKey::TABLE_INDEX] as $keyName => $value) {
                $indexStructureList[] = new IndexStructure(
                    $keyName,
                    $value[SchemaKey::INDEX_TYPE],
                    $value[SchemaKey::INDEX_COLUMN]
                );
            }
        }

        if (array_key_exists(SchemaKey::TABLE_ENGINE, $spec)) {
            $engine = $spec[SchemaKey::TABLE_ENGINE];
        } else {
            $engine = Config::default('engine');
        }

        if (array_key_exists(SchemaKey::TABLE_DEFAULT_CHARSET, $spec)) {
            $defaultCharset = $spec[SchemaKey::TABLE_DEFAULT_CHARSET];
        } else {
            $defaultCharset = Config::default('charset');
        }

        if (array_key_exists(SchemaKey::TABLE_COLLATE, $spec)) {
            $collate = $spec[SchemaKey::TABLE_COLLATE];
        } else {
            $collate = Config::default('collate');
        }

        $properties = array_diff_key($spec, array_flip(SchemaKey::TABLE_KEYS));

        $tableStructure = new TableStructure(
            $tableName,
            $spec[SchemaKey::TABLE_COMMENT],
            $engine,
            $defaultCharset,
            $collate,
            $columnStructureList,
            $indexStructureList,
            $properties
        );
        return $tableStructure;
    }

    /**
     * @param \PDO   $pdo
     * @param string $dbName
     * @param string $tableName
     * @return TableStructure
     */
    public static function fromTable(\PDO $pdo, string $dbName, string $tableName): TableStructure
    {
        $rawStatus = $pdo->query("SHOW TABLE STATUS LIKE '$tableName'")->fetch();
        $rawColumnList = $pdo->query(
            sprintf(
                "SELECT * FROM information_schema.COLUMNS WHERE table_schema = '%s' AND  table_name = '%s' ORDER BY ORDINAL_POSITION ASC",
                $dbName,
                $tableName
            )
        )->fetchAll();

        $rawPartitionList = $pdo->query(
            sprintf(
                "SELECT * FROM information_schema.PARTITIONS WHERE table_schema = '%s' AND  table_name = '%s' ORDER BY PARTITION_ORDINAL_POSITION ASC",
                $dbName,
                $tableName
            )
        )->fetchAll();

        if (count($rawPartitionList) !== 1 and !is_null(reset($rawPartitionList)['PARTITION_METHOD'])) {
            $methods = array_fill_keys(array_column($rawPartitionList, 'PARTITION_METHOD'), []);
            foreach ($rawPartitionList as $item) {
                $methods[$item['PARTITION_METHOD']][] = $item;
            }
            $groups = [];
            foreach ($methods as $method => $methodValue) {
                $expressions = array_fill_keys(array_column($methodValue, 'PARTITION_EXPRESSION'), []);
                foreach ($methodValue as $value) {
                    $expressions[$value['PARTITION_EXPRESSION']][] = $value;
                }
                $groups[$method] = $expressions;
            }
            // dump($groups);
        }


        $columnStructureList = [];

        foreach ($rawColumnList as $column) {
            $attribute = [];
            if ((bool) preg_match('/auto_increment/', $column['EXTRA'])) {
                $attribute[] = Attribute::AUTO_INCREMENT;
            }
            if ('YES' === $column['IS_NULLABLE']) {
                $attribute[] = Attribute::NULLABLE;
            }
            if ((bool) preg_match('/unsigned/', $column['COLUMN_TYPE'])) {
                $attribute[] = Attribute::UNSIGNED;
            }
            $columnStructureList[] = new ColumnStructure(
                $column['COLUMN_NAME'],
                str_replace(' unsigned', '', $column['COLUMN_TYPE']),
                $column['COLUMN_DEFAULT'],
                $column['COLUMN_COMMENT'],
                $attribute,
                []
            );
        }

        $rawIndexList = $pdo->query("SHOW INDEX FROM $tableName")->fetchAll();
        $keyList = [];
        foreach ($rawIndexList as $index) {
            $keyList[$index['Key_name']][] = $index;
        }
        $indexStructureList = [];
        foreach ($keyList as $keyName => $indexList) {
            $indexStructureList[] = new IndexStructure(
                $keyName,
                '0' === $indexList[0]['Non_unique'],
                array_column($indexList, 'Column_name')
            );
        }

        $createQuery = $pdo->query("SHOW CREATE TABLE $tableName")->fetch()[1];
        $defaultCharsetSearch = mb_strstr($createQuery, 'DEFAULT CHARSET=');
        if (false !== $defaultCharsetSearch) {
            $defaultCharsetSearch = str_replace('DEFAULT CHARSET=', '', $defaultCharsetSearch);
            $defaultCharset = explode(' ', $defaultCharsetSearch)[0];
        } else {
            $defaultCharset = null;
        }

        $tableStructure = new TableStructure(
            $tableName,
            $rawStatus['Comment'],
            $rawStatus['Engine'],
            $defaultCharset,
            $rawStatus['Collation'],
            $columnStructureList,
            $indexStructureList,
            []
        );

        return $tableStructure;
    }
}
