<?php

namespace Conv\Migration\Table;

use Conv\MigrationType;
use Conv\Structure\ViewStructureInterface;

/**
 * CREATE VIEW
 */
class ViewCreateMigration extends AbstractTableMigration
{
    /**
     * @param ViewStructureInterface $viewStructure
     */
    public function __construct(
        ViewStructureInterface $viewStructure
    ) {
        $this->tableName = $viewStructure->getViewName();
        $this->type = MigrationType::VIEW_CREATE;

        $this->up = $viewStructure->getCreateQuery();

        $this->down = "DROP VIEW `$this->tableName`;";
    }
}
