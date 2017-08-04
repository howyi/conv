<?php

namespace Conv\Structure;

use Conv\Util\SchemaKey;

class TableStructure
{
    public  $tableName;
    public  $comment;
    public  $engine;
    public  $defaultCharset;
    public  $collate;
    public  $columnStructureList;
    public  $indexStructureList;
    private $properties;

    /**
     * @param string $tableName
     * @param string $comment
     * @param string $engine
     * @param string $defaultCharset
     * @param string $collate
     * @param array  $columnStructureList
     * @param array  $indexStructureList
     * @param array  $properties
     */
    public function __construct(
        string $tableName,
        string $comment,
        string $engine,
        string $defaultCharset,
        string $collate,
        array $columnStructureList,
        array $indexStructureList,
        array $properties
    ) {
        $this->tableName = $tableName;
        $this->comment = $comment;
        $this->engine = $engine;
        $this->defaultCharset = $defaultCharset;
        $this->collate = $collate;
        $this->columnStructureList = $columnStructureList;
        $this->indexStructureList = $indexStructureList;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getEngine(): string
    {
        return $this->engine;
    }

    /**
     * @return string
     */
    public function getDefaultCharset(): string
    {
        return $this->defaultCharset;
    }

    /**
     * @return string
     */
    public function getCollate(): string
    {
        return $this->collate;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getFieldList(): array
    {
        $fieldList = [];
        foreach ($this->columnStructureList as $columnStructure) {
            $fieldList[$columnStructure->field] = (clone $columnStructure);
        }
        return $fieldList;
    }

    /**
     * @param array $diff
     * @return array
     */
    public function getOrderFieldList(array $diff = [], array $renamedFieldList = []): array
    {
        $orderedFieldList = [];
        foreach ($this->columnStructureList as $columnStructure) {
            $field = $columnStructure->field;
            if (in_array($field, $diff, true)) {
                continue;
            }
            if (array_key_exists($field, $renamedFieldList)) {
                $field = $renamedFieldList[$field];
            }
            $orderedFieldList[] = $field;
        }
        return $orderedFieldList;
    }

    /**
     * @param string[] $fieldList
     * @return array
     */
    public function getModifiedColumnList(array $fieldList): array
    {
        $modifiedColumnList = [];
        foreach ($fieldList as $field) {
            $modifiedColumn = new ModifiedColumnStructure(
                $field,
                $this->getFieldList()[$field]
            );
            $orderList = $this->getOrderFieldList();
            $key = array_search($field, $orderList, true) - 1;
            $modifiedColumn->setModifiedAfter(
                array_key_exists($key, $orderList) ? $orderList[$key] : null
            );
            $modifiedColumnList[] = $modifiedColumn;
        }
        return $modifiedColumnList;
    }

    /**
     * @return array
     */
    public function getIndexList(): array
    {
        $indexList = [];
        foreach ($this->indexStructureList as $indexStructure) {
            $indexList[$indexStructure->keyName] = (clone $indexStructure);
        }
        return $indexList;
    }

    /**
     * @param TableStructure $target
     * @return array
     */
    public function getDiffColumnList(TableStructure $target): array
    {
        $removedColumnList = [];
        foreach ($this->getFieldList() as $field => $columnStructure) {
            if (!array_key_exists($field, $target->getFieldList())) {
                $removedColumnList[$field] = $this->getFieldList()[$field];
            }
        }
        return $removedColumnList;
    }

    /**
     * @param TableStructure $target
     * @param array          $renamedFieldList
     * @return array
     */
    public function generateModifiedColumnList(TableStructure $target, array $renamedFieldList): array
    {
        $modifiedColumnList = [];
        foreach ($this->getFieldList() as $beforeField => $before) {
            if (array_key_exists($beforeField, $renamedFieldList)) {
                $afterField = $renamedFieldList[$beforeField];
                $after = $target->getFieldList()[$afterField];
                $modifiedColumnList[$beforeField] = new ModifiedColumnStructureSet(
                    new ModifiedColumnStructure($beforeField, $after),
                    new ModifiedColumnStructure($afterField, $before)
                );
                continue;
            }
            if (!array_key_exists($beforeField, $target->getFieldList())) {
                continue;
            }
            $after = $target->getFieldList()[$beforeField];
            if ($before->isChanged($after)) {
                $modifiedColumnList[$beforeField] = new ModifiedColumnStructureSet(
                    new ModifiedColumnStructure($beforeField, $after),
                    new ModifiedColumnStructure($beforeField, $before)
                );
            }
        }
        return $modifiedColumnList;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            SchemaKey::TABLE_COMMENT => $this->getComment(),
            SchemaKey::TABLE_COLUMN  => [],
        ];
        foreach ($this->columnStructureList as $column) {
            $array[SchemaKey::TABLE_COLUMN][$column->getField()] = $column->toArray();
        }
        $indexList = [];
        foreach ($this->getIndexList() as $index) {
            if ($index->isPrimary) {
                $array[SchemaKey::TABLE_PRIMARY_KEY] = $index->columnNameList;
            } else {
                $indexList[$index->keyName] = $index->toArray();
            }
        }
        if (!empty($indexList)) {
            $array[SchemaKey::TABLE_INDEX] = $indexList;
        }
        return $array;
    }
}
