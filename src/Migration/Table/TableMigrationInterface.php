<?php

namespace Laminaria\Conv\Migration\Table;

interface TableMigrationInterface
{

    /**
     * @return string
     */
    public function getTableName(): string;

    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @return string
     */
    public function getUp(): string;

    /**
     * @return string
     */
    public function getDown(): string;
}
