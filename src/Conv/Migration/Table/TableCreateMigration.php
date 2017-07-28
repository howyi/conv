<?php

namespace Conv\Migration\Table;

use Conv\Structure\TableStructure;

/**
 * CREATE TABLE
 */
class TableCreateMigration extends AbstractTableMigration
{
    /**
     * @param TableStructure $tableStructure
     */
    public function __construct(
        TableStructure $tableStructure
    ) {
        $this->tableName = $tableStructure->tableName;
        $this->type = MigrationType::CREATE;

        $createQueryHeader = "CREATE TABLE `$this->tableName` (";
        foreach ($tableStructure->columnStructureList as $columnStructure) {
            $createQueryLineList[] = $columnStructure->generateCreateQuery();
        }

        foreach ($tableStructure->indexStructureList as $indexStructure) {
            $createQueryLineList[] = $indexStructure->generateCreateQuery();
        }
        $createQueryFooter = ") ENGINE=$tableStructure->engine DEFAULT CHARSET=$tableStructure->defaultCharset COMMENT='$tableStructure->comment';";
        $createQueryBody = "  ".join(',' . PHP_EOL . '  ', $createQueryLineList);
        $this->up = $createQueryHeader . PHP_EOL . $createQueryBody . PHP_EOL . $createQueryFooter;

        // TODO PARTITION

        $this->down = "DROP TABLE `$this->tableName`;";
    }
}
