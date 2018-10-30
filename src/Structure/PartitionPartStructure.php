<?php

namespace Laminaria\Conv\Structure;

class PartitionPartStructure
{
    private $name;
    private $operator;
    private $value;
    private $comment;

    /**
     * @param string $name
     * @param string $operator
     * @param string $value
     * @param string $comment
     */
    public function __construct(
        string $name,
        string $operator,
        string $value,
        string $comment
    ) {
        $this->name = $name;
        $this->operator = $operator;
        $this->value = $value;
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
