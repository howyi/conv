<?php

namespace Conv\Migration\Line;

/**
 * ALTER TABLE ~ KEY ~
 */
class IndexAddMigrationLine extends AbstractMigrationLine
{
    /**
     * @param array[] $indexList
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
