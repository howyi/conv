<?php

namespace Conv\Generator;

use Conv\Migration\Line\IndexAddMigrationLine;
use Conv\Migration\Line\IndexDropMigrationLine;
use Conv\Migration\Line\IndexAllMigrationLine;
use Conv\Structure\IndexStructure;

class IndexMigrationGenerator
{
    /**
     * @param IndexStructure[] $beforeIndexList
     * @param IndexStructure[] $afterIndexList
     * @return IndexAllMigrationLine
     */
    public static function generate(
        array $beforeIndexList,
        array $afterIndexList
    ): IndexAllMigrationLine {
        $firstDownIndexList = array_diff_key($beforeIndexList, $afterIndexList);
        $lastUpIndexList = [];
        foreach ($afterIndexList as $keyName => $afterIndex) {
            if (array_key_exists($keyName, $beforeIndexList)) {
                $beforeIndex = $beforeIndexList[$keyName];
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
