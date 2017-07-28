<?php

namespace Conv\Migration\Line;

/**
 * ALTER TABLE ~ KEY ~
 */
class IndexDropMigrationLine extends AbstractMigrationLine
{
    /**
     * @param array[] $indexList
     */
    public function __construct(array $indexList) {
        $indexAddMigration = new IndexAddMigration($indexList);
        $this->upLineList = $indexAddMigration->getDown();
        $this->downLineList = $indexAddMigration->getUp();
    }
}
