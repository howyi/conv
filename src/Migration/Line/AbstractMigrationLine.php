<?php

namespace Laminaria\Conv\Migration\Line;

abstract class AbstractMigrationLine implements MigrationLineInterface
{
    protected $upLineList = [];
    protected $downLineList = [];

    /**
     * @return string[]
     */
    public function getUp(): array
    {
        return $this->upLineList;
    }

    /**
     * @return string[]
     */
    public function getDown(): array
    {
        return $this->downLineList;
    }
}
