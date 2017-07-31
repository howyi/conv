<?php

namespace Conv\Factory;

use Conv\Structure\ColumnStructure;
use Conv\Structure\IndexStructure;
use Conv\Structure\TableStructure;
use Symfony\Component\Yaml\Yaml;

class TableStructureFactory
{
    const FIELD_ALIAS = [
        'tinyint' => 'tinyint(3)',
        'smallint' => 'smallint(5)',
        'int' => 'int(10)',
        'bigint' => 'bigint(20)',
        'varchar' => 'varchar(255)'
    ];

    const ENGINE = 'InnoDB';
    const DEFAULT_CHARSET = 'utf8mb4';
    const COLLATE = 'utf8mb4_bin';

    const YAML_TABLE_REQUIRE_KEYS = [
        'table',
        'comment',
        'column',
        'primaryKey',
    ];

    const YAML_TABLE_OPTIONAL_KEYS = [
        'index',
    ];

    const YAML_COLUMN_OPTIONAL_KEYS = [
        'alias',
        'type',
        'unsigned',
        'nullable',
        'comment',
    ];

    /**
     * @param string $path
     * @return TableStructure
     */
    public static function fromYaml(string $path): TableStructure
    {
        $yamlSpec = Yaml::parse(file_get_contents($path));

        $columnStructureList = [];
        foreach ($yamlSpec['column'] as $field => $column) {
            $properties = array_diff_key($column, array_flip(self::YAML_COLUMN_OPTIONAL_KEYS));
            $columnStructureList[] = new ColumnStructure(
                $field,
                array_key_exists($column['type'], self::FIELD_ALIAS) ? self::FIELD_ALIAS[$column['type']] : $column['type'],
                $column['comment'],
                array_key_exists('nullable', $column) ? $column['nullable'] : false,
                array_key_exists('unsigned', $column) ? $column['unsigned'] : false,
                array_key_exists('default', $column) ? $column['default'] : null,
                $properties
            );
        }

        $columnStructureList[] = new ColumnStructure('created_at', 'datetime', '作成日時', false, false, null, []);

        $indexStructureList[] = new IndexStructure(
            'PRIMARY',
            $yamlSpec['primaryKey'],
            true
        );

        if (true === array_key_exists('index', $yamlSpec)) {
            foreach ($yamlSpec['index'] as $keyName => $value) {
                $indexStructureList[] = new IndexStructure(
                    $keyName,
                    $value['column'],
                    $value['isUnique']
                );
            }
        }

        $incluedeKeys = array_merge(self::YAML_TABLE_REQUIRE_KEYS, self::YAML_TABLE_OPTIONAL_KEYS);
        $properties = array_diff_key($yamlSpec, array_flip($incluedeKeys));

        $tableStructure = new TableStructure(
            $yamlSpec['table'],
            $yamlSpec['comment'],
            self::ENGINE,
            self::DEFAULT_CHARSET,
            self::COLLATE,
            $columnStructureList,
            $indexStructureList,
            $properties
        );
        return $tableStructure;
    }

    /**
     * @param \PDO    $pdo
      * @param string $tableName
     * @return TableStructure
     */
    public static function fromTable(\PDO $pdo, string $tableName): TableStructure
    {
        $rawStatus = $pdo->query("SHOW TABLE STATUS LIKE '$tableName'")->fetch();
        $rawColumnList = $pdo->query("SHOW FULL COLUMNS FROM $tableName")->fetchAll();
        $columnStructureList = [];

        foreach ($rawColumnList as $column) {
            $columnStructureList[] = new ColumnStructure(
                $column['Field'],
                str_replace(' unsigned', '', $column['Type']),
                $column['Comment'],
                'YES' === $column['Null'],
                (bool) preg_match('/unsigned/', $column['Type']),
                $column['Default'],
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
                array_column($indexList, 'Column_name'),
                '0' === $indexList[0]['Non_unique']
            );
        }

        $tableStructure = new TableStructure(
            $tableName,
            $rawStatus['Comment'],
            self::ENGINE,
            self::DEFAULT_CHARSET,
            self::COLLATE,
            $columnStructureList,
            $indexStructureList,
            []
        );

        return $tableStructure;
    }
}
