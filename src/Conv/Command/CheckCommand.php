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

    protected function execute(InputInterface $input, OutputInterface $output)
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

        $pdo = new \PDO('mysql:host=localhost;dbname=conv;charset=utf8;', 'root', '');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $actualStructure = DatabaseStructureFactory::fromPDO($pdo);
        $expectStructure = DatabaseStructureFactory::fromDir('schema');
        $alter = MigrationGenerator::generate(
            $actualStructure,
            $expectStructure,
            $operator
        );

        dump($alter);
    }
}
