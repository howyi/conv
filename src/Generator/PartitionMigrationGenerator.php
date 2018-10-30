<?php

namespace Laminaria\Conv\Generator;

use Laminaria\Conv\Migration\Line\PartitionMigration;
use Laminaria\Conv\Migration\Line\PartitionRemoveMigration;
use Laminaria\Conv\Migration\Line\PartitionResetMigration;
use Laminaria\Conv\Structure\PartitionStructureInterface;

class PartitionMigrationGenerator
{
    /**
     * @param PartitionStructureInterface|null $beforePartition
     * @param PartitionStructureInterface|null $afterPartition
     * @return PartitionMigration|null
     */
    public static function generate(
        ?PartitionStructureInterface $beforePartition,
        ?PartitionStructureInterface $afterPartition
    ): ?PartitionMigration {
        if (is_null($afterPartition)) {
            if (is_null($beforePartition)) {
                return null;
            } else {
                return new PartitionRemoveMigration($beforePartition);
            }
        }

        if (is_null($beforePartition)) {
            return new PartitionResetMigration(null, $afterPartition);
        }

        if ($beforePartition->getQuery() !== $afterPartition->getQuery()) {
            return new PartitionResetMigration($beforePartition, $afterPartition);
        }

        return null;
    }
}
