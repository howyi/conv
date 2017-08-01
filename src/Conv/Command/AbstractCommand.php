<?php

namespace Conv\Command;

use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{
    public function getPDO($dbname = null): \PDO
    {
        if (is_null($dbname)) {
            $pdo = new \PDO('mysql:host=localhost;charset=utf8;', 'root', '');
        } else {
            $pdo = new \PDO("mysql:host=localhost;dbname=$dbname;charset=utf8;", 'root', '');
        }
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
