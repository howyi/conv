<?php

namespace Laminaria\Conv\Migration\Line;

use Laminaria\Conv\Structure\IndexStructure;

/**
 * ALTER TABLE ~ KEY ~
 */
class IndexDropMigrationLine extends AbstractMigrationLine
{
    /**
     * @param IndexStructure[] $indexList
     */
    public function __construct(array $indexList)
    {
        $indexAddMigration = new IndexAddMigrationLine($indexList);
        $this->upLineList = $indexAddMigration->getDown();
        $this->downLineList = $indexAddMigration->getUp();
    }
}
