<?php

namespace Howyi\Conv\Migration\Line;

/**
 * ALTER TABLE ~ COLLATE ~
 */
class TableCollateMigrationLine extends AbstractMigrationLine
{
    /**
     * @param string $before
     * @param string $after
     */
    public function __construct(
        string $before,
        string $after
    ) {
        $this->upLineList[] = "COLLATE $after";
        $this->downLineList[] = "COLLATE $before";
    }
}
