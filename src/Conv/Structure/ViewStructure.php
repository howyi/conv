<?php

namespace Conv\Structure;

use Conv\Config;
use Conv\Util\SchemaKey;

class ViewStructure implements TableStructureInterface
{
    private $viewName;
    private $aliasList;
    private $columnList;
    private $joinList;
    private $properties;

    /**
     * @param string $viewName
     * @param array  $aliasList
     * @param array  $columnList
     * @param array  $joinList
     * @param array  $properties
     */
    public function __construct(
        string $viewName,
        array $aliasList,
        array $columnList,
        array $joinList,
        array $properties
    ) {
        $this->viewName = $viewName;
        $this->aliasList = $aliasList;
        $this->columnList = $columnList;
        $this->joinList = $joinList;
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
    public function getJoinList(): array
    {
        return $this->joinList;
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
}
