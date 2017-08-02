<?php

namespace Conv\Migration\Table;

interface TableMigrationInterface
{
    /**
     * @return string
     */
    public function getUp(): string;

    /**
     * @return string
     */
    public function getDown(): string;
}
