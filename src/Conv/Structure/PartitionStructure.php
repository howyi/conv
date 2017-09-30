<?php

namespace Conv\Structure;

class PartitionStructure
{
    private $expression;
    private $type;
    private $nameOrder;
    private $parts;

    /**
     * @param string                   $expression
     * @param string                   $type
     * @param string[]                 $nameOrder
     * @param PartitionPartStructure[] $parts
     */
    public function __construct(
        string $expression,
        string $type,
        array $nameOrder,
        array $parts
    ) {
        $this->expression = $expression;
        $this->type = $type;
        $this->nameOrder = $nameOrder;
        $this->parts = $parts;
    }
}
