<?php

namespace Conv\Migration\Line;

use Conv\Structure\IndexStructure;
use Conv\Structure\PartitionStructureInterface;

/**
 * REMOVE PARTITIONING
 */
class PartitionRemoveMigrationLine extends AbstractMigrationLine
{
    /**
    * @param IndexStructure[] $indexList
    */
    public function __construct(PartitionStructureInterface $before)
    {
        $this->upLineList = ['REMOVE PARTITIONING'];
        $this->downLineList = [$before->getQuery()];
    }
}
