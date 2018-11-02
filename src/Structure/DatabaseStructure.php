<?php

namespace Laminaria\Conv\Structure;

class DatabaseStructure
{
    private $tableList = [];

    /**
     * @param TableStructureInterface[] $tableList
     */
    public function __construct(
        array $tableList
    ) {
        $this->tableList = $tableList;
    }

    /**
     * @param array $filter
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
     * @return TableStructure[]
     */
    public function getOnlyTableList(): array
    {
        return $this->getTableList([TableStructureType::TABLE]);
    }

    /**
     * @return ViewStructure[]
     */
    public function getOnlyViewList(): array
    {
        return $this->getTableList([TableStructureType::VIEW, TableStructureType::VIEW_RAW]);
    }

    /**
     * @param DatabaseStructure $target
     * @return TableStructure[]
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
     * @return ViewStructure[]
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
