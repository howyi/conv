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
                        $joinQuery = sprintf(
                            '(%s %s %s using(`%s`))',
                            $joinQuery,
                            $join,
                            $name,
                            $column
                        );
                        $joinedList[] = $factor;
                    }
                    continue;
                }
                if (isset($value[SchemaKey::JOIN_TYPE_ON_EQUAL])) {
                    // $join ~ on()
                    foreach ($value[SchemaKey::JOIN_TYPE_ON_EQUAL] as $key => $factor) {
                        $factorTableName = strstr($factor, '.', true);
                        if (in_array($factorTableName, $joinedList, true)) {
                            continue;
                        }
                        $name = $this->getFullTableName($factorTableName);
                        $column = ltrim(strstr($factor, '.', false), '.');
                        $previousFactor = $value[SchemaKey::JOIN_TYPE_ON_EQUAL][$key - 1];
                        $previousTableName = strstr($previousFactor, '.', true);
                        $previousColumn = ltrim(strstr($previousFactor, '.', false), '.');
                        $joinQuery = sprintf(
                            '(%s %s %s on(`%s`.`%s` = `%s`.`%s`))',
                            $joinQuery,
                            $join,
                            $name,
                            $factorTableName,
                            $column,
                            $previousTableName,
                            $previousColumn
                        );
                        $joinedList[] = $factorTableName;
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
