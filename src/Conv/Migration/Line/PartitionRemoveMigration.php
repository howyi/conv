<?php

namespace Conv\Migration\Line;

use Conv\Structure\IndexStructure;
use Conv\Structure\PartitionStructureInterface;

/**
 * REMOVE PARTITIONING
 */
class PartitionRemoveMigration extends PartitionMigration
{
    /**
    * @param PartitionStructureInterface $before
    */
    public function __construct(PartitionStructureInterface $before)
    {
        $this->up = 'REMOVE PARTITIONING';
        $this->down = $before->getQuery();
    }
}
