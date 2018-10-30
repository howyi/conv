<?php

namespace Laminaria\Conv\Migration\Table;

use Laminaria\Conv\MigrationType;
use Laminaria\Conv\Structure\ViewStructureInterface;

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
