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

    /**
     * @param string $field
     * @param string $type
     * @param string $comment
     * @param bool   $isNullable
     * @param mixed  $isUnsigned
     * @param mixed  $default
     */
    public function __construct(
        string $field,
        string $type,
        string $comment,
        bool $isNullable,
        $isUnsigned,
        $default
    ) {
        $this->field = $field;
        $this->type = $type;
        $this->comment = $comment;
        $this->isNullable = $isNullable;
        $this->isUnsigned = $isUnsigned;
        $this->default = $default;
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
            $this->default === $target->default) {
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
}
