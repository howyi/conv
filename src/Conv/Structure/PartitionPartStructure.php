<?php

namespace Conv\Structure;

class PartitionPartStructure
{
    private $name;
    private $operator;
    private $value;
    private $engine;

    /**
     * @param string $name
     * @param string $operator
     * @param string $value
     * @param string $engine
     */
    public function __construct(
        string $name,
        string $operator,
        string $value,
        string $engine
    ) {
        $this->name = $name;
        $this->operator = $operator;
        $this->value = $value;
        $this->engine = $engine;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return sprintf(
            'PARTITION %s VALUES %s (%s) ENGINE = %s',
            $this->name,
            $this->operator,
            $this->value,
            $this->engine
        );
    }
}
