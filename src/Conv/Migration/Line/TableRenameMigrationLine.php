<?php

namespace Conv\Migration\Line;

/**
 * ALTER TABLE ~ RENAME TO ~
 */
class TableRenameMigrationLine extends AbstractMigrationLine
{
    /**
     * @param string $before
     * @param string $after
     */
    public function __construct(
        string $before,
        string $after
    ) {
        $this->upLineList[] = "RENAME TO `$after`";
        $this->downLineList[] = "RENAME TO `$before`";
    }
}
