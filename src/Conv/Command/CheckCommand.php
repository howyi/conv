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

class CheckCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('check')
            ->setDescription('check');
    }

    protected function execute(Inputinterface $input, OutputInterface $output)
    {
        // $actualStructure = TableStructureFactory::fromYaml(
        //     'sample/actual/tbl_player.yaml'
        // );
        //
        // $expectStructure = TableStructureFactory::fromYaml(
        //     'sample/expected/tbl_player.yaml'
        // );
        //
        // $alter = TableAlterMigrationGenerator::generate(
        //     $actualStructure,
        //     $expectStructure,
        //     $this->getHelper('question'),
        //     $input,
        //     $output
        // );
        // dump($alter);

        $operator = new Operator(
            $this->getHelper('question'),
            $input,
            $output
        );

        $actualStructure = DatabaseStructureFactory::fromDir('sample/actual');
        $expectStructure = DatabaseStructureFactory::fromDir('sample/expected');
        $alter = MigrationGenerator::generate(
            $actualStructure,
            $expectStructure,
            $operator
        );

        dump($alter);
    }
}
