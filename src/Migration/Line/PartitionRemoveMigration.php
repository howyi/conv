<?php

namespace Howyi\Conv\Migration\Line;

use Howyi\Conv\Structure\IndexStructure;
use Howyi\Conv\Structure\PartitionStructureInterface;

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
