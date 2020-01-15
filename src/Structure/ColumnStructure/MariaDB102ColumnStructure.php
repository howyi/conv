<?php

namespace Howyi\Conv\Structure\ColumnStructure;

use Howyi\Conv\Structure\Attribute;
use Howyi\Conv\Util\SchemaKey;

class MariaDB102ColumnStructure extends MySQL57ColumnStructure implements MySQLColumnStructureInterface
{
    /**
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }
}
