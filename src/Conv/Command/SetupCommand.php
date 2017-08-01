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
        $pdo = $this->getPDO();
        $pdo->exec('CREATE DATABASE conv');

        $databaseStructure = DatabaseStructureFactory::fromDir('sample');
        $pdo = $this->getPDO('conv');
        foreach ($databaseStructure->getTableList() as $table) {
            $migration = new TableCreateMigration($table);
            $pdo->exec($migration->getUp());
        }
        $output->writeln('<fg=cyan>setup success</>');
    }
}
