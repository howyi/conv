<?php

namespace Laminaria\Conv\Command;

use Laminaria\Conv\CreateQueryReflector;
use Laminaria\Conv\DatabaseStructureFactory;
use Laminaria\Conv\SchemaReflector;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReflectCommand extends AbstractConvCommand
{
    protected function configure()
    {
        $this
            ->setName('reflect')
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pdo = $this->convertToPdo(
            (string) $input->getArgument('server'),
            $dbName = (string) $input->getArgument('dbName')
        );

        $type = strtolower($input->getOption('type'));

        switch ($type) {
            case 'sql':
                CreateQueryReflector::fromPDO(
                    $pdo,
                    $dbName,
                    (string) $input->getOption('dir'),
                    $this->getOperator($input, $output)
                );
                break;
            case 'yaml':
                $dbStructure = DatabaseStructureFactory::fromPDO($pdo, $dbName);
                SchemaReflector::fromDatabaseStructure(
                    (string) $input->getOption('dir'),
                    $dbStructure,
                    $this->getOperator($input, $output)
                );
                break;
            default:
                throw new \Exception('Unexpected file type (sql or yaml)');
        }
    }
}
