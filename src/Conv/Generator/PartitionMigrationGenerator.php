<?php

namespace Conv\Generator;

use Conv\Migration\Line\AbstractMigrationLine;
use Conv\Migration\Line\PartitionRemoveMigrationLine;
use Conv\Migration\Line\PartitionResetMigrationLine;
use Conv\Structure\PartitionStructureInterface;

class PartitionMigrationGenerator
{
    /**
     * @param PartitionStructureInterface|null $beforePartition
     * @param PartitionStructureInterface|null $afterPartition
     * @return AbstractMigrationLine|null
     */
    public static function generate(
        ?PartitionStructureInterface $beforePartition,
        ?PartitionStructureInterface $afterPartition
    ): ?AbstractMigrationLine {
        if (is_null($afterPartition)) {
            if (is_null($beforePartition)) {
                return null;
            } else {
                return new PartitionRemoveMigrationLine($beforePartition);
            }
        }

        if (is_null($beforePartition)) {
            return new PartitionResetMigrationLine(null, $afterPartition);
        }

        if ($beforePartition->getQuery() !== $afterPartition->getQuery()) {
            return new PartitionResetMigrationLine($beforePartition, $afterPartition);
        }

        return null;
    }
}
