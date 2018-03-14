<?php

namespace Conv\Command;

use Conv\DatabaseStructureFactory;
use Conv\MigrationGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DiffDb2DbCommand extends AbstractConvCommand
{
    protected function configure()
    {
        $this
            ->setName('diff:db2db')
            ->addOption(
                'server1',
                null,
                InputOption::VALUE_OPTIONAL,
                'user:pass@host:port',
                'root@localhost'
            )
            ->addOption(
                'server2',
                null,
                InputOption::VALUE_OPTIONAL,
                'user:pass@host:port',
                'root@localhost'
            )
            ->addArgument(
                'dbNames',
                InputArgument::REQUIRED,
                'db1:db2'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        [$dbName1, $dbName2] = explode(':', $input->getArgument('dbNames'));

        $pdo1 = $this->convertToPdo($input->getOption('server1'), $dbName1);
        $db1Structure = DatabaseStructureFactory::fromPDO($pdo1, $dbName1);

        $pdo2 = $this->convertToPdo($input->getOption('server2'), $dbName2);
        $db2Structure = DatabaseStructureFactory::fromPDO($pdo2, $dbName2);

        $alterMigrations = MigrationGenerator::generate(
            $db2Structure,
            $db1Structure,
            $operator = $this->getOperator($input, $output)
        );

        $this->displayAlterMigration($alterMigrations, $operator);
    }
}
