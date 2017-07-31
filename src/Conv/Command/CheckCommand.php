<?php

namespace Conv\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Conv\Factory\DatabaseStructureFactory;
use Conv\Factory\TableStructureFactory;
use Conv\Generator\TableAlterMigrationGenerator;
use Conv\Generator\MigrationGenerator;

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

        $actualStructure = DatabaseStructureFactory::fromDir('sample/actual');
        $expectStructure = DatabaseStructureFactory::fromDir('sample/expected');
        $alter = MigrationGenerator::generate(
            $actualStructure,
            $expectStructure,
            $this->getHelper('question'),
            $input,
            $output
        );

        dump($alter);
    }
}
