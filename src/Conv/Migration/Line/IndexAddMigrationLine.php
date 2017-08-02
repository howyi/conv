<?php

namespace Conv\Migration\Line;

use Conv\Structure\IndexStructure;

/**
 * ALTER TABLE ~ KEY ~
 */
class IndexAddMigrationLine extends AbstractMigrationLine
{
    /**
     * @param IndexStructure[] $indexList
     */
    public function __construct(
        array $indexList
    ) {
        foreach ($indexList as $index) {
            $this->upLineList[] = $index->generateAddQuery();
            $this->downLineList[] = $index->generateDropQuery();
        }
    }
}
