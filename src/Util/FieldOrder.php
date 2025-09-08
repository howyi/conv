<?php

namespace Howyi\Conv\Util;

class FieldOrder
{
    /**
     * @param string      $field
     * @param string|null $previousAfterField
     * @param string|null $nextAfterField
     */
    public function __construct(private readonly string $field, private $previousAfterField, private $nextAfterField)
    {
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string|null
     */
    public function getPreviousAfterField()
    {
        return $this->previousAfterField;
    }

    /**
     * @return string|null
     */
    public function getNextAfterField()
    {
        return $this->nextAfterField;
    }

    /**
     * @param string
     */
    public function setNextAfterField(string $nextAfterField)
    {
        $this->nextAfterField = $nextAfterField;
    }
}
