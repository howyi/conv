<?php

namespace Conv\Migration\Table;

use Conv\Migration\Line\MigrationLineInterface;

class MigrationLineList
{
    protected $migrationLineList = [];

    /**
     * @param MigrationLineInterface $migrationLine
     */
    public function add(MigrationLineInterface $migrationLine)
    {
        return $this->migrationLineList[] = $migrationLine;
    }


    /**
     * @return bool
     */
    public function isMigratable(): bool
    {
        return !empty($this->migrationLineList);
    }

    /**
     * @return string
     */
    public function getUp(): string
    {
        $upLineList = [];
        foreach ($this->migrationLineList as $migrationLine) {
            $upLineList = array_merge($upLineList, $migrationLine->getUp());
        }
        return '  '.join(',' . PHP_EOL . '  ', $upLineList);
    }

    /**
     * @return string
     */
    public function getDown(): string
    {
        $downLineList = [];
        foreach (array_reverse($this->migrationLineList) as $migrationLine) {
            $downLineList = array_merge($downLineList, $migrationLine->getDown());
        }
        return '  '.join(',' . PHP_EOL . '  ', $downLineList);
    }
}
