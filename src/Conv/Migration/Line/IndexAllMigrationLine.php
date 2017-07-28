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
     * @param IndexDropMigration
     */
    public function setFirst(IndexDropMigration $first)
    {
        return $this->first = $first;
    }

    /**
     * @return bool
     */
    public function getFirst(): IndexDropMigration
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
     * @param IndexAddMigration
     */
    public function setLast(IndexAddMigration $last)
    {
        return $this->last = $last;
    }

    /**
     * @return bool
     */
    public function getLast(): IndexAddMigration
    {
        return $this->last;
    }
}
