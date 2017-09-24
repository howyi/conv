<?php

namespace Conv\Command;

use Conv\DatabaseStructureFactory;
use Conv\Migration\Table\TableCreateMigration;
use Conv\Migration\Table\ViewCreateMigration;
use Symfony\Component\Console\Command\Command;
use Conv\MigrationGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Conv\Structure\TableStructureType;
use Conv\Structure\DatabaseStructure;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('setup');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $operator = $this->getOperator($input, $output);

        $pdo = $this->getPDO();
        $pdo->exec('DROP DATABASE IF EXISTS conv');
        $pdo->exec('CREATE DATABASE conv');

        $databaseStructure = DatabaseStructureFactory::fromDir('schema');
        $empty = new DatabaseStructure([]);
        $alter = MigrationGenerator::generate(
            $empty,
            $databaseStructure,
            $operator
        );

        $pdo = $this->getPDO('conv');
        foreach ($alter->getMigrationList() as $migration) {
            $operator->output('<fg=green>実行クエリ</>');
            $operator->output($migration->getUp());
            $pdo->exec($migration->getUp());
        }
        $output->writeln('<fg=cyan>setup success</>');
    }
}
