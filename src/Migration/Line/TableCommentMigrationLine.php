<?php

namespace Howyi\Conv\Migration\Line;

/**
 * ALTER TABLE ~ COMMENT ~
 */
class TableCommentMigrationLine extends AbstractMigrationLine
{
    /**
     * @param string $before
     * @param string $after
     */
    public function __construct(
        string $before,
        string $after
    ) {
        $this->upLineList[] = "COMMENT '$after'";
        $this->downLineList[] = "COMMENT '$before'";
    }
}
