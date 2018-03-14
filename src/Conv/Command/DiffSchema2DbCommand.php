<?php

namespace Conv\Command;

use Conv\DatabaseStructureFactory;
use Conv\MigrationGenerator;
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
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Reflect file type(sql or yaml)',
                'sql'
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
        $type = strtolower($input->getOption('type'));

        switch ($type) {
            case 'sql':
                $pdo = $this->convertToPdo($input->getOption('root') ?? $input->getArgument('server'));
                $dbSchemaStructure = DatabaseStructureFactory::fromSqlDir(
                    $pdo,
                    $input->getOption('dir'),
                    $this->getOperator($input, $output)
                );
                break;
            case 'yaml':
                $dbSchemaStructure = DatabaseStructureFactory::fromDir($input->getOption('dir'));
                break;
            default:
                throw new \Exception('Unexpected file type (sql or yaml)');
        }

        $pdo = $this->convertToPdo(
            $input->getArgument('server'),
            $dbName = $input->getArgument('dbName')
        );
        $dbStructure = DatabaseStructureFactory::fromPDO($pdo, $dbName);

        $alterMigrations = MigrationGenerator::generate(
            $dbStructure,
            $dbSchemaStructure,
            $operator = $this->getOperator($input, $output)
        );

        $this->displayAlterMigration($alterMigrations, $operator);
    }
}
