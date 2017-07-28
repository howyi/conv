<?php

namespace Conv\Migration\Line;

use Conv\Structure\ModifiedColumnStructureSet;

/**
 * ALTER TABLE ~ MODIFY ~
 */
class ColumnModifyMigrationLine extends AbstractMigrationLine
{
    /**
     * @param ModifiedColumnStructureSet[] $modifiedColumnSetList
     */
    public function __construct(
        array $modifiedColumnSetList
    ) {
        $upChangeQueryLineList = [];
        $upModifyQueryLineList = [];
        $downChangeQueryLineList = [];
        $downModifyQueryLineList = [];

        foreach ($modifiedColumnSetList as $value) {
            $upModifiedColumn = $value->getUpColumn();
            if ($upModifiedColumn->isRenamed() and $upModifiedColumn->isOrderChanged()) {
                $upChangeQueryLineList[] = $upModifiedColumn->generateChangeQuery();
                $upModifyQueryLineList[] = $upModifiedColumn->generateModifyQuery();
            } elseif ($upModifiedColumn->isRenamed()) {
                $upChangeQueryLineList[] = $upModifiedColumn->generateChangeQuery();
            } elseif ($upModifiedColumn->isOrderChanged()) {
                $upModifyQueryLineList[] = $upModifiedColumn->generateModifyQuery();
            } else {
                $upChangeQueryLineList[] = $upModifiedColumn->generateChangeQuery();
            }

            $downModifiedColumn = $value->getDownColumn();
            if ($downModifiedColumn->isRenamed() and $downModifiedColumn->isOrderChanged()) {
                $downChangeQueryLineList[] = $downModifiedColumn->generateChangeQuery();
                $downModifyQueryLineList[] = $downModifiedColumn->generateModifyQuery();
            } elseif ($downModifiedColumn->isRenamed()) {
                $downChangeQueryLineList[] = $downModifiedColumn->generateChangeQuery();
            } elseif ($downModifiedColumn->isOrderChanged()) {
                $downModifyQueryLineList[] = $downModifiedColumn->generateModifyQuery();
            } else {
                $downChangeQueryLineList[] = $downModifiedColumn->generateChangeQuery();
            }
        }

        $this->upLineList = array_merge($upChangeQueryLineList, $upModifyQueryLineList);
        $this->downLineList = array_merge($downChangeQueryLineList, $downModifyQueryLineList);
    }
}
