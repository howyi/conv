<?php

namespace Conv\Structure;

interface TableStructureInterface
{
    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @return string
     */
    public function getName(): string;
}
