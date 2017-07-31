<?php

namespace Conv\Factory;

use Conv\Structure\ColumnStructure;
use Conv\Structure\IndexStructure;
use Conv\Structure\DatabaseStructure;
use Conv\Factory\TableStructureFactory;
use Symfony\Component\Yaml\Yaml;

class DatabaseStructureFactory
{
    /**
     * @param string $path
     */
    public static function fromDir(
        string $path
    ) {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );
        $tableList = [];
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                switch (strtolower($fileinfo->getExtension())) {
                    case 'yml':
                    case 'yaml':
                        $table = TableStructureFactory::fromYaml($fileinfo->getPathName());
                        $tableList[$table->getTableName()] = $table;
                        break;
                    default:
                        break;
                }
            }
        }
        return new DatabaseStructure($tableList);
    }
}
