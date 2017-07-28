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

    const YAML_REQUIRE_KEYS = [
        'table',
        'comment',
        'column',
        'primaryKey'
    ];

    const YAML_OPTIONAL_KEYS = [
        'index'
    ];

    /**
     * @param string $path
     * @return TableStructure
     */
    public static function fromYaml(string $path) : TableStructure
    {
        $yamlSpec = Yaml::parse(file_get_contents($path));

        $columnStructureList = [];
        foreach ($yamlSpec['column'] as $field => $column) {
            $columnStructureList[] = new ColumnStructure(
                $field,
                array_key_exists($column['type'], self::FIELD_ALIAS) ? self::FIELD_ALIAS[$column['type']] : $column['type'],
                $column['comment'],
                array_key_exists('nullable', $column) ? $column['nullable'] : false,
                array_key_exists('unsigned', $column) ? $column['unsigned'] : false,
                array_key_exists('default', $column) ? $column['default'] : null
            );
        }

        $columnStructureList[] = new ColumnStructure('created_at', 'datetime', '作成日時', false, false, null);

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

        $incluedeKeys = array_merge(self::YAML_REQUIRE_KEYS, self::YAML_OPTIONAL_KEYS);
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
}
