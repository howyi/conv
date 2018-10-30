<?php

namespace Laminaria\Conv\Util;

class FieldOrder
{
    private $field;
    private $previousAfterField;
    private $nextAfterField;

    /**
     * @param string      $field
     * @param string|null $previousAfterField
     * @param string|null $nextAfterField
     */
    public function __construct(
        string $field,
        $previousAfterField,
        $nextAfterField
    ) {
        $this->field = $field;
        $this->previousAfterField = $previousAfterField;
        $this->nextAfterField = $nextAfterField;
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
