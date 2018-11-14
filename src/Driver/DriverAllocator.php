<?php

namespace Laminaria\Conv\Driver;

use Composer\Semver\Semver;

class DriverAllocator
{
    /**
     * @param \PDO $PDO
     * @return DriverInterface
     */
    public static function fromPDO(\PDO $PDO): DriverInterface
    {
        $driverName = $PDO->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $version = $PDO->query('select version()')->fetchColumn();

        switch (strtolower($driverName)) {
            case 'mysql':
                switch (true) {
                    case Semver::satisfies($version, '8.0.*'):
                        return new MySQL80Driver($PDO);
                    case Semver::satisfies($version, '5.7.*'):
                        return new MySQL57Driver($PDO);
                    case Semver::satisfies($version, '5.6.*'):
                        return new MySQL56Driver($PDO);
                }
        }

        throw new \RuntimeException('Unsupported driver. conv supported MySQL 5.6.*|5.7.*|8.0.*');
    }
}
