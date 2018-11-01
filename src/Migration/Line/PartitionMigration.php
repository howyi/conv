<?php

namespace Laminaria\Conv\Migration\Line;

abstract class PartitionMigration
{
    protected $up;
    protected $down;

    /**
     * @return string
     */
    public function getUp(): string
    {
        return $this->up;
    }

    /**
     * @return string
     */
    public function getDown(): string
    {
        return $this->down;
    }
}
