<?php

namespace Laminaria\Conv\DebugCommand;

use Laminaria\Conv\DatabaseStructureFactory;
use Laminaria\Conv\Factory\TableStructureFactory;
use Laminaria\Conv\MigrationGenerator;
use Laminaria\Conv\Generator\TableAlterMigrationGenerator;
use Laminaria\Conv\Operator;
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
        $expectStructure = DatabaseStructureFactory::fromDir('tests/Retort/check_schema');
        $alter = MigrationGenerator::generate(
            $actualStructure,
            $expectStructure,
            $operator
        );

        $pdo = $this->getPDO('conv');
        foreach ($alter->getMigrationList() as $migration) {
            $operator->output('<fg=green>実行クエリ</>');
            $operator->output($migration->getUp());
            $operator->output('<fg=green>------</>');
            $operator->output($migration->getDown());
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