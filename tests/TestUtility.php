<?php

namespace Laminaria\Conv;

class TestUtility
{
    /**
     * @param null|string $dbName
     * @return \PDO
     */
    public static function getPdo(?string $dbName = null): \PDO
    {
        $host = getenv('DB_HOST') ? getenv('DB_HOST')  : '127.0.0.1';
        $rootName = getenv('DB_ROOT_NAME') ? getenv('DB_ROOT_NAME')  : 'root';
        $rootPass = getenv('DB_ROOT_PASS') ? getenv('DB_ROOT_PASS')  : '';

        $pdo = new \PDO(
            "mysql:host=$host;charset=utf8;",
            $rootName,
            $rootPass,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        if (!is_null($dbName)) {
            $pdo->exec("USE $dbName");
        }

        return $pdo;
    }
}
