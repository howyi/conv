<?php

namespace Conv\Command;

use Conv\Factory\DatabaseStructureFactory;
use Conv\Migration\Table\TableCreateMigration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{
    protected function configure()
    {
        $this->setName('setup');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pdo = new \PDO('mysql:host=localhost;charset=utf8;', 'root', '');
        $pdo->exec('CREATE DATABASE conv');

        $databaseStructure = DatabaseStructureFactory::fromDir('sample');
        foreach ($databaseStructure->getTableList() as $table) {
            $pdo = new \PDO('mysql:host=localhost;dbname=conv;charset=utf8;', 'root', '');
            $migration = new TableCreateMigration($table);
            dump($migration->getUp());
            $pdo->exec($migration->getUp());
        }
    }
}
