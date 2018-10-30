<?php

namespace Laminaria\Conv\Migration\Line;

use Laminaria\Conv\Structure\ModifiedColumnStructure;

/**
 * ALTER TABLE ~ DROP ~
 */
class ColumnDropMigrationLine extends AbstractMigrationLine
{
    /**
     * @param ModifiedColumnStructure[] $modifiedColumnList
     */
    public function __construct(
        array $modifiedColumnList
    ) {
        $columnAddMigration = new ColumnAddMigrationLine(
            $modifiedColumnList
        );
        $this->upLineList = $columnAddMigration->getDown();
        $this->downLineList = $columnAddMigration->getUp();
    }
}
