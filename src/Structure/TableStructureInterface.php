<?php

namespace Howyi\Conv\Structure;

interface TableStructureInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getName(): string;
}
