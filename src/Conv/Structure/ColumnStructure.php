<?php

namespace Conv\Structure;

use Conv\Util\SchemaKey;

class ColumnStructure
{
    public $field;
    public $type;
    public $default;
    public $comment;
    public $attribute;
    private $properties;

    /**
     * @param string $field
     * @param string $type
     * @param mixed  $default
     * @param string $comment
     * @param array  $attribute
     * @param array  $properties
     */
    public function __construct(
        string $field,
        string $type,
        $default,
        string $comment,
        array $attribute,
        array $properties
    ) {
        $this->field = $field;
        $this->type = $type;
        $this->default = $default;
        $this->comment = $comment;
        $this->attribute = $attribute;
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
        if ($this->isUnsigned() === true) {
            $query[] = 'UNSIGNED';
        }
        if ($this->isNullable() === false) {
            $query[] = 'NOT NULL';
        }

        if ($this->isAutoIncrement() === true) {
            $query[] = 'AUTO_INCREMENT';
        } elseif ($this->default !== null) {
			$query[] = 'DEFAULT';
			$query[] = $this->getDefault();
		} elseif ($this->isNullable() === true) {
			$query[] = 'DEFAULT NULL';
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
            $this->isNullable() === $target->isNullable() and
            $this->isUnsigned() === $target->isUnsigned() and
            $this->default === $target->default and
            $this->isAutoIncrement() === $target->isAutoIncrement()) {
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
        } else {
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
        $array = [
            SchemaKey::COLUMN_TYPE => $this->type
        ];
        if (!is_null($this->default)) {
            $array[SchemaKey::COLUMN_DEFAULT] = $this->default;
        }
        $array[SchemaKey::COLUMN_COMMENT] = $this->comment;
        if (!empty($this->attribute)) {
            $array[SchemaKey::COLUMN_ATTRIBUTE] = $this->attribute;
        }
        return $array;
    }

    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return in_array(Attribute::UNSIGNED, $this->attribute, true);
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return in_array(Attribute::NULLABLE, $this->attribute, true);
    }

    /**
     * @return bool
     */
    public function isAutoIncrement(): bool
    {
        return in_array(Attribute::AUTO_INCREMENT, $this->attribute, true);
    }
}
