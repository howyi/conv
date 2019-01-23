<?php

namespace Howyi\Conv\Migration\Table;

use Howyi\Conv\MigrationType;
use Howyi\Conv\Structure\ViewStructure;

/**
 * RENAME TABLE ~ TO ~
 */
class ViewRenameMigration extends AbstractTableMigration
{
    private $isAltered = false;

    /**
     * @param ViewStructure $beforeView
     * @param ViewStructure $afterView
     */
    public function __construct(
        ViewStructure $beforeView,
        ViewStructure $afterView
    ) {
        $this->tableName = $afterView->getViewName();
        $this->type = MigrationType::VIEW_RENAME;

        $beforeName = $beforeView->getViewName();
        $afterName = $afterView->getViewName();

        $this->up = "RENAME TABLE `$beforeName` TO `$afterName`;";
        $this->down = "RENAME TABLE `$afterName` TO `$beforeName`;";
    }
}
