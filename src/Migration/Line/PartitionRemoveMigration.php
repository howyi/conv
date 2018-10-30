<?php

namespace Laminaria\Conv\Migration\Line;

use Laminaria\Conv\Structure\IndexStructure;
use Laminaria\Conv\Structure\PartitionStructureInterface;

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
