<?php

namespace Conv\Migration\Table;

use Conv\MigrationType;
use Conv\Structure\ViewStructureInterface;

/**
 * CREATE OR REPLACE
 */
class ViewAlterMigration extends AbstractTableMigration
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
        $this->tableName = $beforeView->getViewName();
        $this->type = MigrationType::CREATE_OR_REPLACE;

        $this->isAltered = ($beforeView->getCompareQuery() !== $afterView->getCompareQuery());

        if (!$this->isAltered()) {
            return;
        }

        $this->up = preg_replace('/CREATE/', 'CREATE OR REPLACE', $afterView->getCreateQuery());
        $this->down = preg_replace('/CREATE/', 'CREATE OR REPLACE', $beforeView->getCreateQuery());

        $beforeName = $beforeView->getViewName();
        $afterName = $afterView->getViewName();

        $this->down = str_replace("`$beforeName`", "`$afterName`", $this->down);
    }

    /**
     * @return bool
     */
    public function isAltered(): bool
    {
        return $this->isAltered;
    }
}
