<?php

namespace Howyi\Conv\Driver;

use Howyi\Conv\Structure\Attribute;
use Howyi\Conv\Structure\ColumnStructure\ColumnStructureInterface;
use Howyi\Conv\Structure\ColumnStructure\MySQL57ColumnStructure;
use Howyi\Conv\Structure\ColumnStructure\MySQLColumnStructureInterface;

class MySQL57Driver extends MySQL56Driver
{
    protected function createColumnStructure(array $rawColumn): ColumnStructureInterface
    {
        $attribute = [];
        if ((bool) preg_match('/auto_increment/', $rawColumn['EXTRA'])) {
            $attribute[] = Attribute::AUTO_INCREMENT;
        }
        if ('YES' === $rawColumn['IS_NULLABLE']) {
            $attribute[] = Attribute::NULLABLE;
        }
        if ((bool) preg_match('/unsigned/', $rawColumn['COLUMN_TYPE'])) {
            $attribute[] = Attribute::UNSIGNED;
        }
        if ((bool) preg_match('/STORED/', $rawColumn['EXTRA'])) {
            $attribute[] = Attribute::STORED;
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
            []
        );
    }

    /**
     * @param mixed[] $values
     * @return MySQLColumnStructureInterface
     */
    protected function generateColumnStructure(...$values)
    {
        return new MySQL57ColumnStructure(...$values);
    }
}
