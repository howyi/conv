<?php

namespace Conv\Structure;

class IndexStructure
{
    public $keyName;
    public $columnNameList;
    public $isUnique;
    public $isPrimary;

    /**
     * @param string   $keyName
     * @param string[] $columnNameList
     * @param bool     $isUnique
     */
    public function __construct(
        string $keyName,
        array $columnNameList,
        bool $isUnique
    ) {
        $this->keyName = $keyName;
        $this->columnNameList = $columnNameList;
        $this->isUnique = $isUnique;
        $this->isPrimary = 'PRIMARY' === $this->keyName;
    }

    /**
     * @return string
     */
    public function generateCreateQuery(): string
    {
        $query = [];
        if ($this->isPrimary) {
            $query[] = 'PRIMARY';
            $query[] = 'KEY';
        } else {
            if ($this->isUnique) {
                $query[] = 'UNIQUE';
            }
            $query[] = 'KEY';
            $query[] = "`$this->keyName`";
        }
        $query[] = $this->generateIndexText();
        return implode(' ', $query);
    }

    /**
     * @return string
     */
    public function generateAddQuery(): string
    {
        $query = ['ADD'];
        if ($this->isPrimary) {
            $query[] = 'PRIMARY';
            $query[] = 'KEY';
        } else {
            if ($this->isUnique) {
                $query[] = 'UNIQUE';
            } else {
                $query[] = 'INDEX';
            }
            $query[] = "`$this->keyName`";
        }
        $query[] = $this->generateIndexText();
        return implode(' ', $query);
    }

    /**
     * @return string
     */
    public function generateDropQuery(): string
    {
        if ($this->isPrimary) {
            return 'DROP PRIMARY KEY';
        }
        return "DROP INDEX `$this->keyName`";
    }

    /**
     * @return string
     */
    private function generateIndexText(): string
    {
        return '(`'.join('`, `', $this->columnNameList).'`)';
    }

    /**
     * @param IndexStructure $after
     * @return bool
     */
    public function isChanged(IndexStructure $after): bool
    {
        if ($this->columnNameList === $after->columnNameList and
                $this->isUnique === $after->isUnique) {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'isUnique' => $this->isUnique,
            'column' => $this->columnNameList
        ];
    }
}
