<?php

namespace Conv\Migration\Table;

use Conv\Structure\TableStructure;

/**
 * DROP TABLE
 */
class TableDropMigration extends AbstractTableMigration
{
    /**
     * @param TableStructure $tableStructure
     */
    public function __construct(
        TableStructure $tableStructure
    ) {
        $this->tableName = $tableStructure->tableName;
        $this->type = MigrationType::DROP;

        $tableCreateMigration = new TableCreateMigration($tableStructure);
        $this->up = $tableCreateMigration->getDown();
        $this->down = $tableCreateMigration->getUp();
    }
}
