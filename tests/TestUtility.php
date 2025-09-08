<?php

namespace Howyi\Conv;

class TestUtility
{
    /**
     * @param null|string $dbName
     * @return \PDO[]
     */
    public static function getPdoArray(?string $dbName = null): array
    {
        $hostEnv = getenv('DB_HOST') ?: '127.0.0.1:3306';
        $hosts = explode(',', $hostEnv);
        $rootName = getenv('DB_ROOT_NAME') ?: 'root';
        $rootPass = getenv('DB_ROOT_PASS') ?: '';

        $pdoArray = [];
        foreach ($hosts as $hostEnv) {
            $host = explode(':', $hostEnv)[0];
            $port = explode(':', $hostEnv)[1];
            $pdo = new \PDO(
                "mysql:host=$host;port=$port;charset=utf8;",
                $rootName,
                $rootPass,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );

            if (!is_null($dbName)) {
                $pdo->exec("USE $dbName");
            }

            $pdoArray[] = $pdo;
        }
        return $pdoArray;
    }
}
