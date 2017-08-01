<?php

namespace Conv\Migration\Database;

use Conv\Migration\Table\TableMigrationInterface;

class Migration
{
    protected $migrationList = [];

    /**
     * @param TableMigrationInterface $migration
     */
    public function add(TableMigrationInterface $migration)
    {
        return $this->migrationList[] = $migration;
    }

    /**
     * @return TableMigrationInterface[]
     */
    public function getMigrationList(): array
    {
        return $this->migrationList;
    }
}
