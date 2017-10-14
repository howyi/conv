<?php

namespace Conv\DebugCommand;

use Symfony\Component\Console\Command\Command;
use Conv\Operator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    public function getOperator(InputInterface $input, OutputInterface $output): Operator
    {
        return new Operator(
            $this->getHelper('question'),
            $input,
            $output
        );
    }
}
