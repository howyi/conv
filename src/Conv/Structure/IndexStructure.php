<?php

namespace Conv\Structure;

use Conv\Util\SchemaKey;

class IndexStructure
{
    public $keyName;
    public $isUnique;
	public $indexType;
    public $columnNameList;
    public $isPrimary;
    public $isBtree;

	/**
	 * IndexStructure constructor.
	 * @param string $keyName
	 * @param bool   $isUnique
	 * @param string $indexType
	 * @param array  $columnNameList
	 */
    public function __construct(
        string $keyName,
        bool $isUnique,
		string $indexType,
        array $columnNameList
    ) {
        $this->keyName = $keyName;
        $this->isUnique = $isUnique;
        $this->indexType = $indexType;
        $this->columnNameList = $columnNameList;
        $this->isPrimary = 'PRIMARY' === $this->keyName;
        $this->isBtree = 'BTREE' === strtoupper($this->indexType);
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
            } elseif ($this->isBtree === false) {
				$query[] = strtoupper($this->indexType);
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
            } elseif ($this->isBtree === false) {
				$query[] = strtoupper($this->indexType);
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
        return sprintf(
            '(%s)',
            implode(', ', $this->getQueryNameList())
        );
    }

    /**
     * @return array
     */
    private function getQueryNameList(): array
    {
        $list = [];
        foreach ($this->columnNameList as $name) {
            preg_match('/\(.+?\)/', $name, $match);
            if (!empty($match)) {
                $subPart = $match[0];
                $actualName = str_replace($subPart, '', $name);
                $list[] = "`$actualName`$subPart";
            } else {
                $list[] = "`$name`";
            }
        }
        return $list;
    }

    /**
     * @param IndexStructure $after
     * @return bool
     */
    public function isChanged(IndexStructure $after): bool
    {
        if ($this->columnNameList === $after->columnNameList and
                $this->isUnique === $after->isUnique and
				$this->indexType === $after->indexType) {
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
            SchemaKey::INDEX_TYPE => $this->isUnique,
            SchemaKey::INDEX_COLUMN => $this->columnNameList
        ];
    }
}
