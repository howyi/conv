<?php

namespace Howyi\Conv\Driver;

abstract class AbstractDriver implements DriverInterface
{
    /**
     * @param \PDO $PDO
     */
    public function __construct(private readonly \PDO $PDO)
    {
    }

    /**
     * @return \PDO
     */
    protected function PDO(): \PDO
    {
        return $this->PDO;
    }
}
