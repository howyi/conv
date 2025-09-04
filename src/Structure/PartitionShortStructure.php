<?php

namespace Howyi\Conv\Structure;

class PartitionShortStructure implements PartitionStructureInterface
{
    /**
     * @param string $type
     * @param string $value
     * @param int    $num
     */
    public function __construct(private readonly string $type, private readonly string $value, private readonly int $num)
    {
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        $query = "PARTITION BY {$this->type}({$this->value})" . PHP_EOL;
        $query .= "PARTITIONS {$this->num}";
        return $query;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'by'    => strtolower($this->type),
            'value' => $this->value,
            'num'   => $this->num,
        ];
    }
}
