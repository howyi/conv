<?php

namespace Conv\Generator;

use Conv\Structure\TableStructure;
use Conv\Migration\Line\IndexAddMigrationLine;
use Conv\Migration\Line\IndexDropMigrationLine;
use Conv\Migration\Line\IndexAllMigrationLine;

class IndexMigrationGenerator
{
    public static function generate(
        TableStructure $beforeTable,
        TableStructure $afterTable
    ): IndexAllMigrationLine {
        $firstDownIndexList = [];
        $lastUpIndexList = [];
        foreach ($afterTable->getIndexList() as $keyName => $afterIndex) {
            if (array_key_exists($keyName, $beforeTable->getIndexList())) {
                $beforeIndex = $beforeTable->getIndexList()[$keyName];
                if ($afterIndex->isChanged($beforeIndex)) {
                    $firstDownIndexList[] = $beforeIndex;
                    $lastUpIndexList[] = $afterIndex;
                } else {
                    continue;
                }
            } else {
                $lastUpIndexList[] = $afterIndex;
            }
        }

        $indexAllMigration = new IndexAllMigrationLine();

        if (0 !== count($firstDownIndexList)) {
            $indexAllMigration->setFirst(
                new IndexDropMigrationLine($firstDownIndexList)
            );
        }

        if (0 !== count($lastUpIndexList)) {
            $indexAllMigration->setLast(
                new IndexAddMigrationLine($lastUpIndexList)
            );
        }

        return $indexAllMigration;
    }
}
