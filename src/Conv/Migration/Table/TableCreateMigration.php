<?php

namespace Conv\Migration\Table;

use Conv\MigrationType;
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
        $createQueryLineList = [];

        foreach ($tableStructure->columnStructureList as $columnStructure) {
            $createQueryLineList[] = $columnStructure->generateCreateQuery();
        }

        foreach ($tableStructure->indexStructureList as $indexStructure) {
            $createQueryLineList[] = $indexStructure->generateCreateQuery();
        }
        $createQueryFooter = ")";
        $createQueryFooter .= " ENGINE=$tableStructure->engine";
        $createQueryFooter .= " DEFAULT CHARSET=$tableStructure->defaultCharset";
        $createQueryFooter .= " COLLATE=$tableStructure->collate";
        $createQueryFooter .= " COMMENT='$tableStructure->comment'";
        if (!is_null($tableStructure->getPartition())) {
            $partitionQuery = $tableStructure->getPartition()->getQuery();
            $partitionQuery = sprintf("/*!50100 %s  */", $partitionQuery);
            $createQueryFooter .= PHP_EOL . $partitionQuery;
        }
        $createQueryFooter .= ';';
        $createQueryBody = "  ".join(',' . PHP_EOL . '  ', $createQueryLineList);
        $this->up = $createQueryHeader . PHP_EOL . $createQueryBody . PHP_EOL . $createQueryFooter;

        $this->down = "DROP TABLE `$this->tableName`;";
    }
}
