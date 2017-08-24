<?php

namespace Conv\Command;

use Conv\DatabaseStructureFactory;
use Conv\Factory\TableStructureFactory;
use Conv\MigrationGenerator;
use Conv\Generator\TableAlterMigrationGenerator;
use Conv\Operator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Conv\Structure\TableStructureInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Conv\SchemaReflector;

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
        SchemaReflector::fromDatabaseStructure('schema', $structure, $this->getOperator($input, $output));
    }
}
