<?php

namespace Laminaria\Conv\DebugCommand;

use Laminaria\Conv\CreateQueryReflector;
use Laminaria\Conv\DatabaseStructureFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Laminaria\Conv\Operator\DropOnlySilentOperator;

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
        CreateQueryReflector::fromPDO($this->getPDO('conv'), 'conv', 'database', new DropOnlySilentOperator());
        // SchemaReflector::fromDatabaseStructure('tests/Retort/check_schema', $structure, $this->getOperator($input, $output));
    }
}
