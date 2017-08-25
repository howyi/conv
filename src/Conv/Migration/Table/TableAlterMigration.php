<?php

namespace Conv\Migration\Table;

use Conv\MigrationType;

/**
 * ALTER TABLE
 */
class TableAlterMigration extends AbstractTableMigration
{
    private $isAltered = false;

    /**
     * @param string          $beforeTableName
     * @param string          $afterTableName
     * @param MigrationLineList $migrationLineList
     */
    public function __construct(
        string $beforeTableName,
        string $afterTableName,
        MigrationLineList $migrationLineList
    ) {
        $this->tableName = $beforeTableName;
        $this->type = MigrationType::ALTER;

        $this->isAltered = $migrationLineList->isMigratable();

        if (!$this->isAltered()) {
            return;
        }

        $queryHeaderTemplate = "ALTER TABLE `%s`";

        $upBody = $migrationLineList->getUp();
        $this->up = sprintf($queryHeaderTemplate, $beforeTableName) . PHP_EOL . $upBody . ';';

        $downBody = $migrationLineList->getDown();
        $this->down = sprintf($queryHeaderTemplate, $afterTableName) . PHP_EOL . $downBody . ';';

        // TODO PARTITION
    }

    /**
     * @return bool
     */
    public function isAltered(): bool
    {
        return $this->isAltered;
    }
}
