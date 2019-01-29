<?php

namespace Howyi\Conv\Structure;

interface PartitionStructureInterface
{
    /**
     * @return string
     */
    public function getQuery(): string;

    /**
     * @return array
     */
    public function toArray(): array;
}
