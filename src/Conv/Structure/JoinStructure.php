<?php

namespace Conv\Structure;

use Conv\Util\SchemaKey;

class JoinStructure
{
    private $joinArray;
    private $aliasList;

    /**
     * @param array $joinArray
     * @param array $aliasList
     */
    public function __construct(
        array $joinArray,
        array $aliasList
    ) {
        $this->joinArray = $joinArray;
        $this->aliasList = $aliasList;
    }

    /**
     * @return string
     */
    public function genareteJoinQuery(): string
    {
        $joinQuery = $this->getFullTableName($this->joinArray[SchemaKey::JOIN_REFERENCE]);
        $joinedList = [$this->joinArray[SchemaKey::JOIN_REFERENCE]];

        foreach ($this->joinArray[SchemaKey::JOIN_JOINS] as $values) {
            foreach ($values as $joinType => $value) {
                $join = str_replace('_', ' ', $joinType);
                if (isset($value[SchemaKey::JOIN_TYPE_USING])) {
                    // $join ~ using()
                    foreach ($value[SchemaKey::JOIN_TYPE_USING][SchemaKey::JOIN_USING_FACTOR] as $factor) {
                        if (in_array($factor, $joinedList, true)) {
                            continue;
                        }
                        $name = $this->getFullTableName($factor);
                        $column = $value[SchemaKey::JOIN_TYPE_USING][SchemaKey::JOIN_USING_COLUMN];
                        $joinQuery = "($joinQuery $join $name using(`$column`))";
                        $joinedList[] = $factor;
                    }
                    continue;
                }
            }
        }
        return $joinQuery;
    }

    /**
     * @return string
     */
    private function getFullTableName(string $alias): string
    {
        $fullTableName = '';
        if (in_array($alias, $this->aliasList, true)) {
            $fullTableName .= sprintf(
                '`%s` ',
                array_search($alias, $this->aliasList, true)
            );
        }
        $fullTableName .= "`$alias`";
        return $fullTableName;
    }
}
