<?php

namespace Howyi\Conv\Migration\Table;

use Howyi\Conv\MigrationType;
use Howyi\Conv\Structure\ViewStructure;

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
        $this->type = MigrationType::VIEW_CREATE;

        $this->up = $viewStructure->getCreateQuery();

        $this->down = "DROP VIEW `$this->tableName`;";
    }
}
