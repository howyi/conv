<?php

namespace Howyi\Conv\Migration\Database;

use Howyi\Conv\Migration\Table\TableMigrationInterface;
use Howyi\Conv\Migration\Table\ViewAlterMigration;

class Migration
{
    protected $migrationList = [];

    /**
     * @param TableMigrationInterface $migration
     */
    public function add(TableMigrationInterface $migration)
    {
        $this->migrationList[] = $migration;
    }

    /**
     * @param ViewAlterMigration $migration
     */
    public function addSplit(ViewAlterMigration $migration)
    {
        // array_unshift($this->migrationList,  new ViewAlterOnlyDownMigration($migration));
        // $this->migrationList[] = new ViewAlterOnlyUpMigration($migration);
    }

    /**
     * @return TableMigrationInterface[]
     */
    public function getMigrationList(): array
    {
        return $this->migrationList;
    }
}
