<?php

namespace Howyi\Conv\DebugCommand;

use Howyi\Conv\CreateQueryReflector;
use Howyi\Conv\DatabaseStructureFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Howyi\Conv\Operator\DropOnlySilentOperator;

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

        return 0;
    }
}
