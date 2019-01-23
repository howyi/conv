<?php

namespace Howyi\Conv\Structure;

use Howyi\Conv\Util\SchemaKey;

class JoinStructure
{
    private $joinArray;
    private $aliasList;

    const MYSQL_OPERATOR = [
      '=',
      '<=>',
      '<>',
      '!=',
      '<',
      '<=',
      '>',
      '=>',
    ];

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
        $reference = $this->joinArray[SchemaKey::JOIN_REFERENCE];

        foreach ($this->joinArray[SchemaKey::JOIN_JOINS] as $values) {
            foreach ($values as $joinType => $value) {
                $join = strtolower(str_replace('_', ' ', $joinType));
                $factor = $value[SchemaKey::JOIN_FACTOR];
                if (in_array($factor, $joinedList, true)) {
                    continue;
                }
                $name = $this->getFullTableName($factor);

                $equal = $value[SchemaKey::JOIN_ON];
                $equal = $this->graveDecorator($equal);

                $joinQuery = "($joinQuery $join $name on(($equal)))";
                $joinedList[] = $factor;
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

    /**
     * @param string $text
     * @return string
     */
    private function graveDecorator(string $text): string
    {
        preg_match_all('/\(.+?\)/', $text, $match);
        $match = $match[0];
        if (empty($match)) {
            return $this->gravePartDecorator($text);
        } else {
            foreach ($match as $part) {
                $originPart = ltrim(rtrim($part, ')'), '(');
                $part = $this->gravePartDecorator($originPart);
                $text = preg_replace("/$originPart/", $part, $text, 1);
            }
            return $text;
        }
    }

    /**
     * @param string $text
     * @return string
     */
    private function gravePartDecorator(string $text): string
    {
        $replace = [];
        $replaced = [];
        $id = 100001;
        $sep = 'R_%d_R';
        foreach (explode(' ', trim($text)) as $pieces) {
            if (in_array($pieces, self::MYSQL_OPERATOR) or ' ' === $pieces or is_numeric($pieces)) {
                continue;
            }
            foreach (explode('.', trim($pieces)) as $piece) {
                $insert = sprintf($sep, $id);
                $text = preg_replace("/$piece/", $insert, $text, 1);
                $replace[] = $insert;
                $replaced[] =  "`$piece`";
                $id++;
            }
        }
        return str_replace($replace, $replaced, $text);
    }
}
