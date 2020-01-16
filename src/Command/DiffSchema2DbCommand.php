<?php

namespace Howyi\Conv\Command;

use Howyi\Conv\DatabaseStructureFactory;
use Howyi\Conv\MigrationGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DiffSchema2DbCommand extends AbstractConvCommand
{
    protected function configure()
    {
        $this
            ->setName('diff:schema2db')
            ->addArgument(
                'server',
                InputArgument::REQUIRED,
                'user:pass@host:port'
            )
            ->addArgument(
                'dbName',
                InputArgument::REQUIRED,
                'dbName'
            )
            ->addOption(
                'dir',
                'd',
                InputOption::VALUE_REQUIRED,
                'Save directory'
            )
            ->addOption(
                'root',
                'r',
                InputOption::VALUE_OPTIONAL,
                'root server (user:pass@host:port)',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pdo = $this->convertToPdo((string) $input->getOption('root') ?? (string) $input->getArgument('server'));
        $dbSchemaStructure = DatabaseStructureFactory::fromSqlDir(
            $pdo,
            (string) $input->getOption('dir'),
            $this->getOperator($input, $output)
        );

        $pdo = $this->convertToPdo(
            (string) $input->getArgument('server'),
            $dbName = (string) $input->getArgument('dbName')
        );
        $dbStructure = DatabaseStructureFactory::fromPDO($pdo, $dbName);

        $alterMigrations = MigrationGenerator::generate(
            $dbStructure,
            $dbSchemaStructure,
            $operator = $this->getOperator($input, $output)
        );

        $this->displayAlterMigration($alterMigrations, $operator);

        return 0;
    }
}
