<?php

namespace Laminaria\Conv\Migration\Table;

use Laminaria\Conv\Migration\Line\PartitionMigration;
use Laminaria\Conv\MigrationType;

/**
 * ALTER TABLE
 */
class TableAlterMigration extends AbstractTableMigration
{
    private $isAltered = false;
    private $renamedNameList;

    /**
     * @param string                  $beforeTableName
     * @param string                  $afterTableName
     * @param MigrationLineList       $migrationLineList
     * @param array                   $renamedNameList
     * @param PartitionMigration|null $partitionMigration
     */
    public function __construct(
        string $beforeTableName,
        string $afterTableName,
        MigrationLineList $migrationLineList,
        array $renamedNameList,
        ?PartitionMigration $partitionMigration
    ) {
        $this->tableName = $beforeTableName;
        $this->type = MigrationType::ALTER;
        $this->renamedNameList = $renamedNameList;

        $this->isAltered = ($migrationLineList->isMigratable() or !is_null($partitionMigration));

        if (!$this->isAltered()) {
            return;
        }

        $queryHeaderTemplate = "ALTER TABLE `%s`";

        $this->up = sprintf($queryHeaderTemplate, $beforeTableName);
        $this->down = sprintf($queryHeaderTemplate, $afterTableName);

        if ($migrationLineList->isMigratable()) {
            $upBody = $migrationLineList->getUp();
            $this->up .= PHP_EOL . $upBody;

            $downBody = $migrationLineList->getDown();
            $this->down .= PHP_EOL . $downBody;
        }

        if (!is_null($partitionMigration)) {
            $this->up .= PHP_EOL . $partitionMigration->getUp();
            $this->down .= PHP_EOL . $partitionMigration->getDown();
        }
        $this->up .= ';';
        $this->down .= ';';
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
