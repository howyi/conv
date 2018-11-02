<?php

namespace Laminaria\Conv\Command;

use Laminaria\Conv\CreateQueryReflector;
use Laminaria\Conv\DatabaseStructureFactory;
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pdo = $this->convertToPdo(
            (string) $input->getArgument('server'),
            $dbName = (string) $input->getArgument('dbName')
        );

        CreateQueryReflector::fromPDO(
            $pdo,
            $dbName,
            (string) $input->getOption('dir'),
            $this->getOperator($input, $output)
        );
    }
}
