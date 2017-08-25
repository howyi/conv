<?php

namespace Conv\Migration\Table;

use Conv\MigrationType;
use Conv\Structure\ViewStructure;

/**
 * DROP VIEW
 */
class ViewDropMigration extends AbstractTableMigration
{
    /**
     * @param ViewStructure $viewStructure
     */
    public function __construct(
        ViewStructure $viewStructure
    ) {
        $this->tableName = $viewStructure->getViewName();
        $this->type = MigrationType::DROP;

        $viewCreateMigration = new ViewCreateMigration($viewStructure);
        $this->up = $viewCreateMigration->getDown();
        $this->down = $viewCreateMigration->getUp();
    }
}
