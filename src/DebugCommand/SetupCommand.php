<?php

namespace Laminaria\Conv\DebugCommand;

use Laminaria\Conv\CreateQueryReflector;
use Laminaria\Conv\DatabaseStructureFactory;
use Laminaria\Conv\Migration\Table\TableCreateMigration;
use Laminaria\Conv\Migration\Table\ViewCreateMigration;
use Symfony\Component\Console\Command\Command;
use Laminaria\Conv\MigrationGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Laminaria\Conv\Structure\TableStructureType;
use Laminaria\Conv\Structure\DatabaseStructure;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('setup');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $operator = $this->getOperator($input, $output);
		//
        // $pdo = $this->getPDO();
        // $pdo->exec('DROP DATABASE IF EXISTS conv');
        // $pdo->exec('CREATE DATABASE conv');
		//
        // $databaseStructure = DatabaseStructureFactory::fromDir('tests/Retort/test_schema/008');
        // $empty = new DatabaseStructure([]);
        // $alter = MigrationGenerator::generate(
        //     $empty,
        //     $databaseStructure,
        //     $operator
        // );
		//
        // $pdo = $this->getPDO('conv');
        // foreach ($alter->getMigrationList() as $migration) {
        //     $operator->output('<fg=green>実行クエリ</>');
        //     $operator->output($migration->getUp());
        //     $pdo->exec($migration->getUp());
        // }
        // CreateQueryReflector::fromPDO(
        // 	$pdo,
		// 	'conv',
		// 	'tests/Retort/test_schema_sql/008',
		// 	$operator
		// );
        // $output->writeln('<fg=cyan>setup success</>');
    }
}
