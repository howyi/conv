<?php

namespace Laminaria\Conv\Migration\Line;

use Laminaria\Conv\Structure\PartitionStructureInterface;

/**
 * PARTITION BY ~
 */
class PartitionResetMigration extends PartitionMigration
{
    /**
    * @param PartitionStructureInterface|null $before
    * @param PartitionStructureInterface      $after
    */
    public function __construct(
        ?PartitionStructureInterface $before,
        PartitionStructureInterface $after
    ) {
        $this->up = $after->getQuery();
        $this->down = is_null($before) ? 'REMOVE PARTITIONING' : $before->getQuery();
    }
}
