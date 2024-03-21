<?php

namespace Howyi\Conv\Driver;

use Composer\Semver\Semver;

class DriverAllocator
{
    const TYPE_MYSQL = 'mysql';
    const TYPE_MARIA_DB = 'mariadb';

    /**
     * @param \PDO $PDO
     * @return DriverInterface
     */
    public static function fromPDO(\PDO $PDO): DriverInterface
    {
        $driverName = $PDO->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $rawVersion = $PDO->getAttribute(\PDO::ATTR_SERVER_VERSION);
        preg_match(
            "/^[0-9\.]+/",
            $rawVersion,
            $match
        );

        $type = $version = null;

        if (strpos($rawVersion, 'MariaDB') !== false) {
            $type = self::TYPE_MARIA_DB;
            $split = explode('-', $rawVersion);
            if (strtolower($split[1]) === 'mariadb') {
                // MariaDB 11 ~
                $version = $split[0];
            } else {
                $version = $split[1];
            }
        } elseif (strtolower($driverName) === 'mysql') {
            $type = self::TYPE_MYSQL;
            $version = $match[0];
        }

        switch ($type) {
            case self::TYPE_MYSQL:
                switch (true) {
                    case Semver::satisfies($version, '>= 8.0.0'):
                        return new MySQL80Driver($PDO);
                    case Semver::satisfies($version, '5.7.*'):
                        return new MySQL57Driver($PDO);
                    case Semver::satisfies($version, '5.6.*'):
                        return new MySQL56Driver($PDO);
                }
            case self::TYPE_MARIA_DB:
                switch (true) {
                    case Semver::satisfies($version, '>= 10.2.0'):
                        return new MariaDB102Driver($PDO);
                    case Semver::satisfies($version, '10.1.*'):
                        return new MySQL57Driver($PDO);
                    case Semver::satisfies($version, '10.0.*'):
                        return new MySQL56Driver($PDO);
                }
        }

        throw new \RuntimeException('Unsupported driver. conv supported MySQL 5.6~ and MariaDB 10.0~');
    }
}
