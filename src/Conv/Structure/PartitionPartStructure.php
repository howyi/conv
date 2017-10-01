<?php

namespace Conv\Structure;

class PartitionPartStructure
{
    private $name;
    private $operator;
    private $value;
    private $engine;
    private $comment;

    /**
     * @param string      $name
     * @param string      $operator
     * @param string      $value
     * @param string|null $engine
     * @param string|null $comment
     */
    public function __construct(
        string $name,
        string $operator,
        string $value,
        $engine,
        $comment
    ) {
        $this->name = $name;
        $this->operator = $operator;
        $this->value = $value;
        $this->engine = $engine;
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        $query = sprintf(
            'PARTITION %s VALUES %s (%s)',
            $this->name,
            $this->operator,
            $this->value
        );

        if (false === is_null($this->engine)) {
            $query .= " ENGINE = {$this->engine}";
        }

        if (false === is_null($this->comment)) {
            $query .= " COMMENT = '{$this->comment}'";
        }

        return $query;
    }
}
