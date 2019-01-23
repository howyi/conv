<?php

namespace Howyi\Conv\DebugCommand;

use Symfony\Component\Console\Command\Command;
use Howyi\Conv\Operator\ConsoleOperator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    public function getPDO($dbname = null): \PDO
    {
        if (is_null($dbname)) {
            $pdo = new \PDO('mysql:host=127.0.0.1;charset=utf8;', 'root', '');
        } else {
            $pdo = new \PDO("mysql:host=127.0.0.1;dbname=$dbname;charset=utf8;", 'root', '');
        }
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function getOperator(InputInterface $input, OutputInterface $output): ConsoleOperator
    {
        return new ConsoleOperator(
            $this->getHelper('question'),
            $input,
            $output
        );
    }
}
