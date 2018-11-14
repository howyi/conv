<?php

namespace Laminaria\Conv\Driver;

use Laminaria\Conv\Structure\TableStructure;
use Laminaria\Conv\Structure\ViewStructure;

interface DriverInterface
{
    /**
     * @param string $dbName
     * @param string $tableName
     * @return TableStructure
     */
    public function createTableStructure(string $dbName, string $tableName): TableStructure;

    /**
     * @param string $viewName
     * @return ViewStructure
     */
    public function createViewStructure(string $viewName): ViewStructure;
}
