<?php

namespace Conv\Structure;

class ColumnStructure
{
    public $field;
    public $type;
    public $comment;
    public $isUnsigned = false;
    public $isNullable = false;
    public $default;
    public $autoIncrement;
    private $properties;

    /**
     * @param string $field
     * @param string $type
     * @param string $comment
     * @param bool   $isNullable
     * @param mixed  $isUnsigned
     * @param mixed  $default
     * @param bool   $autoIncrement
     * @param array  $properties
     */
    public function __construct(
        string $field,
        string $type,
        string $comment,
        bool $isNullable,
        $isUnsigned,
        $default,
        bool $autoIncrement,
        array $properties
    ) {
        $this->field = $field;
        $this->type = $type;
        $this->comment = $comment;
        $this->isNullable = $isNullable;
        $this->isUnsigned = $isUnsigned;
        $this->default = $default;
        $this->autoIncrement = $autoIncrement;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function generateCreateQuery(): string
    {
        $query = ["`$this->field`"];
        $query[] = $this->generateBaseQuery();
        return implode(' ', $query);
    }

    /**
     * @return string
     */
    public function generateBaseQuery(): string
    {
        $query = [$this->type];
        if ($this->isUnsigned === true) {
            $query[] = 'UNSIGNED';
        }
        if ($this->isNullable === false) {
            $query[] = 'NOT NULL';
        }
        if (!is_null($this->default)) {
            $query[] = 'DEFAULT';
            $query[] = $this->getDefault();
        } elseif ($this->autoIncrement) {
            $query[] = 'AUTO_INCREMENT';
        }
        $query[] = 'COMMENT';
        $query[] = "'$this->comment'";
        return implode(' ', $query);
    }

    /**
     * @return string
     */
    public function generateDropQuery(): string
    {
        return "DROP COLUMN `$this->field`";
    }

    /**
     * @param ColumnStructure $target
     * @return bool
     */
    public function isChanged(ColumnStructure $target): bool
    {
        if ($this->type === $target->type and
            $this->comment === $target->comment and
            $this->isNullable === $target->isNullable and
            $this->isUnsigned === $target->isUnsigned and
            $this->default === $target->default and
            $this->autoIncrement === $target->autoIncrement) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        if (is_numeric($this->default)) {
            return $this->default;
        } else{
            return "'$this->default'";
        }
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $array['type'] = $this->type;
        if ($this->isUnsigned) {
            $array['unsigned'] = true;
        }
        if (!is_null($this->default)) {
            $array['default'] = $this->default;
        }
        if ($this->autoIncrement) {
            $array['autoIncrement'] = true;
        }
        $array['nullable'] = $this->isNullable;
        $array['comment'] = $this->comment;
        return $array;
    }
}
