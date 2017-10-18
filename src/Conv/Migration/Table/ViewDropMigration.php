<?php

namespace Conv\Migration\Table;

use Conv\MigrationType;
use Conv\Structure\ViewStructureInterface;

/**
 * DROP VIEW
 */
class ViewDropMigration extends AbstractTableMigration
{
    /**
     * @param ViewStructureInterface $viewStructure
     */
    public function __construct(
        ViewStructureInterface $viewStructure
    ) {
        $this->tableName = $viewStructure->getViewName();
        $this->type = MigrationType::VIEW_DROP;

        $viewCreateMigration = new ViewCreateMigration($viewStructure);
        $this->up = $viewCreateMigration->getDown();
        $this->down = $viewCreateMigration->getUp();
    }
}
