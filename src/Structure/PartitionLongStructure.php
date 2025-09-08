<?php

namespace Howyi\Conv\Structure;

class PartitionLongStructure implements PartitionStructureInterface
{
    /**
     * @param string                   $type
     * @param string                   $value
     * @param PartitionPartStructure[] $parts
     */
    public function __construct(
        private readonly string $type,
        private readonly string $value,
        private array $parts
    ) {
        ksort($this->parts);
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        $query = "PARTITION BY {$this->type}({$this->value}) (" . PHP_EOL;
        $partsLineList = [];
        foreach ($this->parts as $part) {
            $partsLineList[] = $part->getQuery();
        }
        $query .= '  ' . join(',' . PHP_EOL . '  ', $partsLineList);
        $query .= ')';
        return $query;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $list = [];
        foreach ($this->parts as $part) {
            $partList[strtolower($part->getOperator())] = $part->getValue();
            if (!empty($part->getComment())) {
                $partList['comment'] = $part->getComment();
            }
            $list[$part->getName()] = $partList;
        }
        return [
            'by'    => strtolower($this->type),
            'value' => $this->value,
            'list'  => $list,
        ];
    }
}
