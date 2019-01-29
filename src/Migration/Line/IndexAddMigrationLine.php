<?php

namespace Howyi\Conv\Migration\Line;

use Howyi\Conv\Structure\IndexStructure;

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
