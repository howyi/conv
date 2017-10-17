<?php

namespace Conv\Structure;

use Conv\Util\Config;
use Conv\Util\SchemaKey;

class ViewRawStructure implements ViewStructureInterface, TableStructureInterface
{
    private $viewName;
    private $createQuery;
    private $properties;

    /**
     * @param string $viewName
     * @param string $createQuery
     * @param array  $properties
     */
    public function __construct(
        string $viewName,
        string $createQuery,
        array $properties
    ) {
        $this->viewName = $viewName;
        $this->createQuery = $createQuery;
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
     * @return string
     */
    public function getCreateQuery(): string
    {
        $query = $this->createQuery;
        $definer = ' DEFINER' . explode('DEFINER', $query)[1] . 'DEFINER';
        $query = str_replace(
          $definer,
          '',
          $query
        );
        $query = str_replace(
          'AS select ',
          PHP_EOL . 'AS select' . PHP_EOL . '  ',
          $query
        );
        $query = str_replace(
          ',',
          ',' . PHP_EOL . '  ',
          $query
        );
        $query = str_replace(
          ' from ',
          ' ' . PHP_EOL . 'from' . PHP_EOL . '  ',
          $query
        );
        $query = str_replace(
          ' where ',
          ' ' . PHP_EOL . 'where' . PHP_EOL . '  ',
          $query
        );
        return $query;
    }

    /**
     * @return string
     */
    public function getRawQuery(): string
    {
        return $this->createQuery;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return TableStructureType::VIEW_RAW;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->viewName;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            SchemaKey::TABLE_TYPE => $this->getType(),
            SchemaKey::VIEW_RAW_QUERY => $this->getRawQuery(),
        ];
    }

    /**
     * @return string
     */
    public function getCompareQuery(): string
    {
        $definer = ' DEFINER' . explode('DEFINER', $this->createQuery)[1] . 'DEFINER';
        $compareQuery = str_replace([$definer, PHP_EOL, ' '], '', $this->createQuery);
        $compareQuery = str_replace("`$this->viewName`", 'TABLENAME', $compareQuery);
        return rtrim($compareQuery, ';');
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
