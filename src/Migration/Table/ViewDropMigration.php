<?php

namespace Laminaria\Conv\Migration\Table;

use Laminaria\Conv\MigrationType;
use Laminaria\Conv\Structure\ViewStructure;

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
        $this->type = MigrationType::VIEW_DROP;

        $viewCreateMigration = new ViewCreateMigration($viewStructure);
        $this->up = $viewCreateMigration->getDown();
        $this->down = $viewCreateMigration->getUp();
    }
}
