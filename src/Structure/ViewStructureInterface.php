<?php

namespace Laminaria\Conv\Structure;

interface ViewStructureInterface
{
    /**
     * @return string
     */
    public function getViewName(): string;

    /**
     * @return string
     */
    public function getCreateQuery(): string;

    /**
     * @return string
     */
    public function getCompareQuery(): string;
}
