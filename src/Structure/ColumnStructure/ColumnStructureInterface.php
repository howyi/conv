<?php

namespace Laminaria\Conv\Structure\ColumnStructure;

interface ColumnStructureInterface
{
    /**
     * @return string
     */
    public function generateCreateQuery(): string;

    /**
     * @return string
     */
    public function generateDropQuery(): string;

    /**
     * @return string
     */
    public function generateBaseQuery(): string;
}
