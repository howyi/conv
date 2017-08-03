<?php

namespace Conv\Factory;

use Conv\Structure\ColumnStructure;
use Conv\Structure\IndexStructure;
use Conv\Structure\Attribute;
use Conv\Structure\TableStructure;
use Symfony\Component\Yaml\Yaml;
use Conv\Util\SchemaKey;
use Conv\Config;
use Conv\Util\SchemaValidator;

class TableStructureFactory
{
    const ENGINE          = 'InnoDB';
    const DEFAULT_CHARSET = 'utf8mb4';
    const COLLATE         = 'utf8mb4_bin';

    /**
     * @param string $path
     * @return TableStructure
     */
    public static function fromYaml(string $path): TableStructure
    {
        $tableName = pathinfo($path, PATHINFO_FILENAME);

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
        $yamlSpec = Yaml::parse(file_get_contents($path));
        SchemaValidator::validate($path, $yamlSpec);
        restore_error_handler();

        $columnStructureList = [];
        foreach ($yamlSpec[SchemaKey::TABLE_COLUMN] as $field => $column) {
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
        if (true === array_key_exists(SchemaKey::TABLE_PRIMARY_KEY, $yamlSpec)) {
            $indexStructureList[] = new IndexStructure(
                'PRIMARY',
                true,
                $yamlSpec[SchemaKey::TABLE_PRIMARY_KEY]
            );
        }

        if (true === array_key_exists(SchemaKey::TABLE_INDEX, $yamlSpec)) {
            foreach ($yamlSpec[SchemaKey::TABLE_INDEX] as $keyName => $value) {
                $indexStructureList[] = new IndexStructure(
                    $keyName,
                    $value[SchemaKey::INDEX_TYPE],
                    $value[SchemaKey::INDEX_COLUMN]
                );
            }
        }

        $properties = array_diff_key($yamlSpec, array_flip(SchemaKey::TABLE_KEYS));

        $tableStructure = new TableStructure(
            $tableName,
            $yamlSpec[SchemaKey::TABLE_COMMENT],
            Config::table('engine'),
            Config::table('default_charset'),
            Config::table('collate'),
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
            $attribute = [];
            if ((bool) preg_match('/auto_increment/', $column['Extra'])) {
                $attribute[] = Attribute::AUTO_INCREMENT;
            }
            if ('YES' === $column['Null']) {
                $attribute[] = Attribute::NULLABLE;
            }
            if ((bool) preg_match('/unsigned/', $column['Type'])) {
                $attribute[] = Attribute::UNSIGNED;
            }
            $columnStructureList[] = new ColumnStructure(
                $column['Field'],
                str_replace(' unsigned', '', $column['Type']),
                $column['Default'],
                $column['Comment'],
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
