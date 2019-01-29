<?php

namespace Howyi\Conv\Migration\Line;

/**
 * ALTER TABLE ~ DEFAULT CHARSET ~
 */
class TableDefaultCharsetMigrationLine extends AbstractMigrationLine
{
    /**
     * @param string $before
     * @param string $after
     */
    public function __construct(
        string $before,
        string $after
    ) {
        $this->upLineList[] = "DEFAULT CHARSET $after";
        $this->downLineList[] = "DEFAULT CHARSET $before";
    }
}
