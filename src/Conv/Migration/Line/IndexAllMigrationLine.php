<?php

namespace Conv\Migration\Line;

/**
 * ALTER TABLE ~ KEY ~
 */
class IndexAllMigrationLine
{
    private $first = null;
    private $last  = null;

    /**
     * @return bool
     */
    public function isFirstExist()
    {
        return !is_null($this->first);
    }

    /**
     * @param IndexDropMigrationLine
     */
    public function setFirst(IndexDropMigrationLine $first)
    {
        return $this->first = $first;
    }

    /**
     * @return bool
     */
    public function getFirst(): IndexDropMigrationLine
    {
        return $this->first;
    }

    /**
     * @return bool
     */
    public function isLastExist()
    {
        return !is_null($this->last);
    }

    /**
     * @param IndexAddMigrationLine
     */
    public function setLast(IndexAddMigrationLine $last)
    {
        return $this->last = $last;
    }

    /**
     * @return bool
     */
    public function getLast(): IndexAddMigrationLine
    {
        return $this->last;
    }
}
