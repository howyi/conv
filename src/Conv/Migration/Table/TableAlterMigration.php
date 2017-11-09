<?php

namespace Conv\Migration\Table;

use Conv\MigrationType;

/**
 * ALTER TABLE
 */
class TableAlterMigration extends AbstractTableMigration
{
    private $isAltered = false;
    private $renamedNameList;

    /**
     * @param string          $beforeTableName
     * @param string          $afterTableName
     * @param MigrationLineList $migrationLineList
     * @param array           $renamedNameList
     */
    public function __construct(
        string $beforeTableName,
        string $afterTableName,
        MigrationLineList $migrationLineList,
        array $renamedNameList
    ) {
        $this->tableName = $beforeTableName;
        $this->type = MigrationType::ALTER;
        $this->renamedNameList = $renamedNameList;

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

    /**
     * @return array
     */
    public function renamedNameList(): array
    {
        return $this->renamedNameList;
    }
}
