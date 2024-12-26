<?php

namespace Howyi\Conv\Driver;

use Howyi\Conv\Structure\TableStructure;
use Howyi\Conv\Structure\ViewStructure;

interface DriverInterface
{
    /**
     * @param string $dbName
     * @param string $tableName
     * @return TableStructure
     */
    public function createTableStructure(string $dbName, string $tableName): TableStructure;

    /**
	 * @param string $dbName
     * @param string $viewName
     * @return ViewStructure
     */
    public function createViewStructure(string $dbName, string $viewName): ViewStructure;
}
