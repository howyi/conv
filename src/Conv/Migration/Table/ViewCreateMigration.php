<?php

namespace Conv\Migration\Table;

use Conv\MigrationType;
use Conv\Structure\ViewStructure;

/**
 * CREATE VIEW
 */
class ViewCreateMigration extends AbstractTableMigration
{
    /**
     * @param ViewStructure $viewStructure
     */
    public function __construct(
        ViewStructure $viewStructure
    ) {
        $this->tableName = $viewStructure->getViewName();
        $this->type = MigrationType::CREATE;

        $this->up = $viewStructure->getCreateQuery();

        $this->down = "DROP VIEW `$this->tableName`;";
    }
}
