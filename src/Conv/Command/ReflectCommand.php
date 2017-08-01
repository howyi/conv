<?php

namespace Conv\Command;

use Conv\Factory\DatabaseStructureFactory;
use Conv\Factory\TableStructureFactory;
use Conv\Generator\MigrationGenerator;
use Conv\Generator\TableAlterMigrationGenerator;
use Conv\Util\Operator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Conv\Util\SchemaReflector;

class ReflectCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('reflect')
            ->setDescription('reflect');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pdo = new \PDO('mysql:host=localhost;dbname=conv', 'root', '');
        $structure = DatabaseStructureFactory::fromPDO(
            $pdo
        );

        SchemaReflector::fromDatabaseStructure(
            'sample/actual',
            $structure
        );
    }
}
