<?php

namespace Conv\Structure;

use Conv\Util\Config;
use Conv\Util\SchemaKey;

class ViewStructure implements ViewStructureInterface, TableStructureInterface
{
    private $viewName;
    private $algorithm;
    private $aliasList;
    private $columnList;
    private $joinStructure;
    private $properties;

    /**
     * @param string      $viewName
     * @param string|null $algorithm
     * @param array       $aliasList
     * @param array       $columnList
     * @param array       $joinArray
     * @param array       $properties
     */
    public function __construct(
        string $viewName,
        $algorithm,
        array $aliasList,
        array $columnList,
        array $joinArray,
        array $properties
    ) {
        $this->viewName = $viewName;
        $this->algorithm = $algorithm;
        $this->aliasList = $aliasList;
        $this->columnList = $columnList;
        $this->joinStructure = new JoinStructure($joinArray, $aliasList);
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getViewName(): string
    {
        return $this->viewName;
    }

    /**
     * @return string|null
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @return array
     */
    public function getAliasList(): array
    {
        return $this->aliasList;
    }

    /**
     * @return array
     */
    public function getColumnList(): array
    {
        return $this->columnList;
    }

    /**
     * @return array
     */
    public function getJoinStructure(): JoinStructure
    {
        return $this->joinStructure;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return TableStructureType::VIEW;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->viewName;
    }

    /**
     * @return string
     */
    public function getCreateQuery(): string
    {
        $createQuery = '';
        if (is_null($this->getAlgorithm())) {
            $createQuery = "CREATE VIEW `$this->viewName`". PHP_EOL;
        } else {
            $algorithm = strtoupper($this->getAlgorithm());
            $createQuery = "CREATE ALGORITHM=$algorithm VIEW `$this->viewName`". PHP_EOL;
        }
        $createQuery .= 'AS select' . PHP_EOL;

        $bodyList = [];
        foreach ($this->getColumnList() as $field => $value) {
            $targetTableName = strstr($value, '.', true);
            $targetColumn = ltrim(strstr($value, '.', false), '.');
            $bodyList[] = "`$targetTableName`.`$targetColumn` AS `$field`";
        }
        $createQuery .= "  ".join(',' . PHP_EOL . '  ', $bodyList) . PHP_EOL;
        $createQuery .= 'from' . PHP_EOL;
        $createQuery .= '  ' . $this->getJoinStructure()->genareteJoinQuery() . ';' . PHP_EOL;

        return $createQuery;
    }

    /**
     * @return string
     */
    public function getCompareQuery(): string
    {
        $createQuery = $this->getCreateQuery();
        $compareQuery = str_replace([PHP_EOL, ' '], '', $createQuery);
        $compareQuery = str_replace("`$this->viewName`", 'TABLENAME', $compareQuery);
        return rtrim($compareQuery, ';');
    }
}
