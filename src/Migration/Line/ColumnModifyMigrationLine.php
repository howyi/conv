<?php

namespace Howyi\Conv\Migration\Line;

use Howyi\Conv\Structure\ModifiedColumnStructureSet;

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
        $downChangeQueryLineList = [];

        foreach ($modifiedColumnSetList as $value) {
            $this->upLineList[] = $value->getUpColumn()->generateChangeQuery();
            $this->downLineList[] = $value->getDownColumn()->generateChangeQuery();
        }
    }
}
