<?php

namespace Laminaria\Conv\Migration\Line;

/**
 * ALTER TABLE ~ ENGINE ~
 */
class TableEngineMigrationLine extends AbstractMigrationLine
{
    /**
     * @param string $before
     * @param string $after
     */
    public function __construct(
        string $before,
        string $after
    ) {
        $this->upLineList[] = "ENGINE $after";
        $this->downLineList[] = "ENGINE $before";
    }
}
