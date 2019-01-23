<?php

namespace Howyi\Conv\Driver;

abstract class AbstractDriver implements DriverInterface
{
    private $PDO;

    /**
     * @param \PDO $PDO
     */
    public function __construct(\PDO $PDO)
    {
        $this->PDO = $PDO;
    }

    /**
     * @return \PDO
     */
    protected function PDO(): \PDO
    {
        return $this->PDO;
    }
}
