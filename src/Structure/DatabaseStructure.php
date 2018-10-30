<?php

namespace Laminaria\Conv\Structure;

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
        $filter = [TableStructureType::TABLE];
        $removedTableList = [];
        foreach ($this->getTableList($filter) as $tableName => $tableStructure) {
            if (!array_key_exists($tableName, $target->getTableList($filter))) {
                $removedTableList[$tableName] = $this->getTableList()[$tableName];
            }
        }
        return $removedTableList;
    }

    /**
     * @param DatabaseStructure $target
     * @return array
     */
    public function getDiffViewList(DatabaseStructure $target): array
    {
        $filter = [TableStructureType::VIEW, TableStructureType::VIEW_RAW];
        $removedTableList = [];
        foreach ($this->getTableList($filter) as $tableName => $tableStructure) {
            if (!array_key_exists($tableName, $target->getTableList($filter))) {
                $removedTableList[$tableName] = $this->getTableList()[$tableName];
            }
        }
        return $removedTableList;
    }
}
