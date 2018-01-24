<?php

namespace Conv\Migration\Line;

use Conv\Structure\PartitionStructureInterface;

/**
 * PARTITION BY ~
 */
class PartitionResetMigrationLine extends AbstractMigrationLine
{
    /**
    * @param PartitionStructureInterface|null $before
    * @param PartitionStructureInterface      $after
    */
    public function __construct(
        ?PartitionStructureInterface $before,
        PartitionStructureInterface $after
    ) {
        $this->upLineList = [$after->getQuery()];
        $this->downLineList = is_null($before) ? ['REMOVE PARTITIONING'] : [$before->getQuery()];
    }
}
