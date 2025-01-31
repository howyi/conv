<?php

namespace Howyi\Conv\Driver;

use Howyi\Conv\Structure\ColumnStructure\ColumnStructureInterface;
use Howyi\Conv\Structure\ColumnStructure\TiDBTempColumnStructure;
use Howyi\Conv\Structure\ColumnStructure\MySQLColumnStructureInterface;
use Howyi\Conv\Structure\TableStructure;
use Howyi\Conv\Structure\Attribute;

class TiDBTempDriver extends MySQL80Driver
{
    protected function createColumnStructureList(string $dbName, string $tableName): array
    {
        $attribute = [];
        $format = <<<EOT
SELECT *
FROM information_schema.COLUMNS
WHERE table_schema = '%s' AND  table_name = '%s'
ORDER BY ORDINAL_POSITION ASC
EOT;

        $rawColumnList = $this->PDO()->query(
            sprintf(
                $format,
                $dbName,
                $tableName
            )
        )->fetchAll();

        $rawPkStatus = $this->PDO()->query("SELECT TIDB_ROW_ID_SHARDING_INFO FROM information_schema.TABLES WHERE table_name = '$tableName'")->fetch();

        $columnStructureList = [];

        foreach ($rawColumnList as $rawColumn) {
            $columnStructureList[] = $this->createColumnStructure($rawColumn, $rawPkStatus['TIDB_ROW_ID_SHARDING_INFO']);
        }

        return $columnStructureList;
    }



    protected function createColumnStructure(array $rawColumn, ?string $rawPkStatus = null): ColumnStructureInterface
    {
        $attribute = [];
        $auto_random = null;
        if ('YES' === $rawColumn['IS_NULLABLE']) {
            $attribute[] = Attribute::NULLABLE;
        }
        if ((bool) preg_match('/unsigned/', $rawColumn['COLUMN_TYPE'])) {
            $attribute[] = Attribute::UNSIGNED;
        }
        if ((bool) preg_match('/STORED/', $rawColumn['EXTRA'])) {
            $attribute[] = Attribute::STORED;
        }

        if ((bool) preg_match('/PK_AUTO_RANDOM/', $rawPkStatus) && $rawColumn['COLUMN_KEY'] == 'PRI') {
            preg_match_all('/[0-9]+/', $rawPkStatus, $bit_range);
            $auto_random = [
              'AUTO_RANDOM',
                $bit_range[0][0],
                $bit_range[0][1]
            ];
        }

        $collationName = $rawColumn['COLLATION_NAME'];
        $generationExpression = empty($rawColumn['GENERATION_EXPRESSION']) ? null : $rawColumn['GENERATION_EXPRESSION'];

        return $this->generateColumnStructure(
            $rawColumn['COLUMN_NAME'],
            str_replace(' unsigned', '', $rawColumn['COLUMN_TYPE']),
            $rawColumn['COLUMN_DEFAULT'],
            $rawColumn['COLUMN_COMMENT'],
            $attribute,
            $collationName,
            $generationExpression,
            [],
            $auto_random
        );
    }



    /**
     * @param mixed[] 
     * @return MySQLColumnStructureInterface
     */
    protected function generateColumnStructure(...$values)
    {
        return new TiDBTempColumnStructure(...$values);
    }
}
