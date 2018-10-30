<?php

namespace Laminaria\Conv\Migration\Table;

use Laminaria\Conv\MigrationType;
use Laminaria\Conv\Structure\ViewStructureInterface;

/**
 * RENAME TABLE ~ TO ~
 */
class ViewRenameMigration extends AbstractTableMigration
{
    private $isAltered = false;

    /**
     * @param ViewStructureInterface $beforeView
     * @param ViewStructureInterface $afterView
     * @param MigrationLineList $migrationLineList
     */
    public function __construct(
        ViewStructureInterface $beforeView,
        ViewStructureInterface $afterView
    ) {
        $this->tableName = $afterView->getViewName();
        $this->type = MigrationType::VIEW_RENAME;

        $beforeName = $beforeView->getViewName();
        $afterName = $afterView->getViewName();

        $this->up = "RENAME TABLE `$beforeName` TO `$afterName`;";
        $this->down = "RENAME TABLE `$afterName` TO `$beforeName`;";
    }
}
