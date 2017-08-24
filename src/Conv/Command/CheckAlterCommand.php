<?php

namespace Conv\Command;

use Conv\DatabaseStructureFactory;
use Conv\Factory\TableStructureFactory;
use Conv\MigrationGenerator;
use Conv\Generator\TableAlterMigrationGenerator;
use Conv\Util\Operator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckAlterCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('check:alter');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $operator = $this->getOperator($input, $output);

        $actualStructure = DatabaseStructureFactory::fromPDO($this->getPDO('conv'), 'conv');
        $expectStructure = DatabaseStructureFactory::fromDir('schema');
        $alter = MigrationGenerator::generate(
            $actualStructure,
            $expectStructure,
            $operator
        );

        $pdo = $this->getPDO('conv');
        foreach ($alter->getMigrationList() as $migration) {
            $operator->output('<fg=green>実行クエリ</>');
            $operator->output($migration->getUp());
            $pdo->exec($migration->getUp());
        }

        $actualStructure = DatabaseStructureFactory::fromPDO($pdo, 'conv');
        $alter = MigrationGenerator::generate(
            $actualStructure,
            $expectStructure,
            $operator
        );

        $operator->output(sprintf('<fg=cyan>DBとの差分:%d</>', count($alter->getMigrationList())));
    }
}
