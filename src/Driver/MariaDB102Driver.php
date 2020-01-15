<?php

namespace Howyi\Conv\Driver;

use Howyi\Conv\Structure\ColumnStructure\MariaDB102ColumnStructure;

class MariaDB102Driver extends MySQL57Driver
{

    /**
     * {@inheritdoc}
     */
    protected function generateColumnStructure(...$values)
    {
        return new MariaDB102ColumnStructure(...$values);
    }
}
