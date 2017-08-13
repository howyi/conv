<?php

namespace Conv\Migration\Table;

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

        $createQueryHeader = "CREATE VIEW `$this->tableName`" . PHP_EOL . 'AS SELECT';
        $createSelectLineList = $viewStructure->generateSelectedLineList();
        $createFromLineList = $viewStructure->generateFromLineList();

        $createSelectQueryBody = "  ".join(',' . PHP_EOL . '  ', $createSelectLineList);
        $createFromQueryBody = "  ".join(',' . PHP_EOL . '  ', $createFromLineList);
        $this->up = $createQueryHeader . PHP_EOL . $createSelectQueryBody . PHP_EOL . $createFromQueryBody;

        // TODO PARTITION

        $this->down = "DROP TABLE `$this->tableName`;";
    }
}
