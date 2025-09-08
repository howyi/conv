<?php

namespace Howyi\Conv\Structure;

class PartitionPartStructure
{
    /**
     * @param string $name
     * @param string $operator
     * @param string $value
     * @param string $comment
     */
    public function __construct(private readonly string $name, private readonly string $operator, private readonly string $value, private readonly string $comment)
    {
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        $query = sprintf(
            'PARTITION %s VALUES %s (%s)',
            $this->name,
            strtoupper($this->operator),
            $this->value
        );

        if (false === empty($this->getComment())) {
            $query .= " COMMENT = '{$this->comment}'";
        }

        return $query;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}
