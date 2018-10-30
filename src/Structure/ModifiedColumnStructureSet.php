<?php

namespace Laminaria\Conv\Structure;

class ModifiedColumnStructureSet
{
    public $upColumn;
    public $downColumn;

    /**
     * @param ModifiedColumnStructure $upColumn
     * @param ModifiedColumnStructure $downColumn
     */
    public function __construct(
        ModifiedColumnStructure $upColumn,
        ModifiedColumnStructure $downColumn
    ) {
        $this->upColumn = $upColumn;
        $this->downColumn = $downColumn;
    }

    /**
     * @return ModifiedColumnStructure
     */
    public function getUpColumn(): ModifiedColumnStructure
    {
        return $this->upColumn;
    }

    /**
     * @return ModifiedColumnStructure
     */
    public function getDownColumn(): ModifiedColumnStructure
    {
        return $this->downColumn;
    }
}
