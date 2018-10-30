<?php

namespace Laminaria\Conv\DebugCommand;

use Laminaria\Conv\CreateQueryReflector;
use Laminaria\Conv\DatabaseStructureFactory;
use Laminaria\Conv\Factory\TableStructureFactory;
use Laminaria\Conv\MigrationGenerator;
use Laminaria\Conv\Generator\TableAlterMigrationGenerator;
use Laminaria\Conv\Operator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Laminaria\Conv\Structure\TableStructureInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Laminaria\Conv\SchemaReflector;

class ReflectCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('reflect')
            ->setDescription('reflect');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $structure = DatabaseStructureFactory::fromPDO(
        //     $this->getPDO('conv'),
        //     'conv',
        //     function(TableStructureInterface $table) {
        //         return (bool) preg_match('/user/', $table->getName());
        //     }
        // );
        $structure = DatabaseStructureFactory::fromPDO(
            $this->getPDO('conv'),
            'conv'
        );
		CreateQueryReflector::fromPDO($this->getPDO('conv'), 'conv', 'database');
        // SchemaReflector::fromDatabaseStructure('tests/Retort/check_schema', $structure, $this->getOperator($input, $output));
    }
}
