<?php

namespace Howyi\Conv\Migration\Table;

abstract class AbstractTableMigration implements TableMigrationInterface
{
    protected $tableName;
    protected $type;
    protected $up;
    protected $down;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUp(): string
    {
        return $this->up;
    }

    /**
     * @return string
     */
    public function getDown(): string
    {
        return $this->down;
    }
}
