<?php

namespace Conv\Structure;

class DatabaseStructure
{
    private $tableList = [];

    /**
     * @param array  $tableList
     */
    public function __construct(
        array $tableList
    ) {
        $this->tableList = $tableList;
    }

    /**
     * @return array
     */
    public function getTableList(): array
    {
        return $this->tableList;
    }

    /**
     * @param DatabaseStructure $target
     * @return array
     */
    public function getDiffTableList(DatabaseStructure $target): array
    {
        $removedTableList = [];
        foreach ($this->getTableList() as $tableName => $tableStructure) {
            if (!array_key_exists($tableName, $target->getTableList())) {
                $removedTableList[$tableName] = $this->getTableList()[$tableName];
            }
        }
        return $removedTableList;
    }
}
