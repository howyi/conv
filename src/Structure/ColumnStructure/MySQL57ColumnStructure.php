<?php

namespace Howyi\Conv\Structure\ColumnStructure;

use Howyi\Conv\Structure\Attribute;
use Howyi\Conv\Util\SchemaKey;

class MySQL57ColumnStructure implements MySQLColumnStructureInterface
{
    use MySQL56ColumnStructureTrait;

    public $generationExpression;

    /**
     * @param string      $field
     * @param string      $type
     * @param mixed       $default
     * @param string      $comment
     * @param array       $attribute
     * @param string|null $collationName
     * @param string|null $generationExpression
     * @param array       $properties
     */
    public function __construct(
        string $field,
        string $type,
        $default,
        string $comment,
        array $attribute,
        ?string $collationName,
        ?string $generationExpression,
        array $properties
    ) {
        $this->field = $field;
        $this->type = $type;
        $this->default = $default;
        $this->comment = $comment;
        $this->attribute = $attribute;
        $this->collationName = $collationName;
        $this->generationExpression = $generationExpression;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function generateBaseQuery(): string
    {
        $query = [$this->type];
        if ($this->isUnsigned() === true) {
            $query[] = 'UNSIGNED';
        }

        if ($this->collationName !== null) {
            $query[] = 'COLLATE';
            $query[] = $this->collationName;
        }

        if ($this->generationExpression !== null) {
            $query[] = 'AS';
            $query[] = "($this->generationExpression)";
        }

        if ($this->isStored() === true) {
            $query[] = 'STORED';
        }

        if ($this->isNullable() === false) {
            $query[] = 'NOT NULL';
        } elseif ($this->isForceNull()) {
            $query[] = 'NULL';
        }

        if ($this->generationExpression === null) {
            if ($this->isAutoIncrement() === true) {
                $query[] = 'AUTO_INCREMENT';
            } elseif ($this->default !== null) {
                $query[] = 'DEFAULT';
                $query[] = $this->getDefault();
            } elseif ($this->isNullable() === true) {
                $query[] = 'DEFAULT NULL';
            }
        }

        $query[] = 'COMMENT';
        $query[] = "'$this->comment'";
        return implode(' ', $query);
    }

    /**
     * @param MySQL57ColumnStructure $target
     * @return bool
     */
    public function isChanged(MySQL57ColumnStructure $target): bool
    {
        if ($this->type === $target->type and
            $this->comment === $target->comment and
            $this->isNullable() === $target->isNullable() and
            $this->isUnsigned() === $target->isUnsigned() and
            $this->default === $target->default and
            $this->isAutoIncrement() === $target->isAutoIncrement() and
            $this->collationName === $this->collationName and
            $this->generationExpression === $this->generationExpression and
            $this->isStored() === $this->isStored()) {
            return false;
        }
        return true;
    }
}
