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
     * @return array $filter
     * @return array
     */
    public function getTableList(array $filter = []): array
    {
        $list = [];
        foreach ($this->tableList as $name => $structure) {
            if (!empty($filter) and !in_array($structure->getType(), $filter, true)) {
                continue;
            }
            $list[$name] = $structure;
        }
        return $list;
    }

    /**
     * @param DatabaseStructure $target
     * @return array
     */
    public function getDiffTableList(DatabaseStructure $target): array
    {
        $removedTableList = [];
        foreach ($this->getTableList([TableStructureType::TABLE]) as $tableName => $tableStructure) {
            if (!array_key_exists($tableName, $target->getTableList([TableStructureType::TABLE]))) {
                $removedTableList[$tableName] = $this->getTableList()[$tableName];
            }
        }
        return $removedTableList;
    }
}
