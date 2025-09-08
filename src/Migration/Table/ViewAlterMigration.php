<?php

namespace Howyi\Conv\Migration\Table;

use Howyi\Conv\MigrationType;
use Howyi\Conv\Structure\ViewStructure;

/**
 * CREATE OR REPLACE
 */
class ViewAlterMigration extends AbstractTableMigration
{
    private $isAltered = false;
    private $isSplit = false;

    /**
     * @param ViewStructure $beforeView
     * @param ViewStructure $afterView
     * @param array         $allRenamedNameList
     */
    public function __construct(
        ViewStructure $beforeView,
        ViewStructure $afterView,
        array $allRenamedNameList
    ) {
        $this->tableName = $afterView->getViewName();
        $this->type = MigrationType::CREATE_OR_REPLACE;

        $this->isAltered = ($beforeView->getCompareQuery() !== $afterView->getCompareQuery());

        if (!$this->isAltered()) {
            return;
        }

        $this->up = preg_replace('/CREATE/', 'CREATE OR REPLACE', $afterView->getCreateQuery());
        $this->down = preg_replace('/CREATE/', 'CREATE OR REPLACE', $beforeView->getCreateQuery());

        foreach ($allRenamedNameList as $renamedNameList) {
            $count = 0;
            foreach ($renamedNameList as $name) {
                $count += (!str_contains((string) $this->up, (string) $name)) ? 0 : 1;
            }
            if ($count === count($renamedNameList)) {
                $this->isSplit = true;
            }
        }

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

    /**
     * @return bool
     */
    public function isSplit(): bool
    {
        return $this->isSplit;
    }
}
