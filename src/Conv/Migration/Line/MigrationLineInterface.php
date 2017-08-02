<?php

namespace Conv\Migration\Line;

interface MigrationLineInterface
{
    /**
     * @return string[]
     */
    public function getUp(): array;

    /**
     * @return string[]
     */
    public function getDown(): array;
}
