<?php

namespace Laminaria\Conv\DebugCommand;

use Laminaria\Conv\Util\Config;
use Laminaria\Conv\Factory\ViewStructureFactory;
use Laminaria\Conv\MigrationGenerator;
use Laminaria\Conv\Generator\TableAlterMigrationGenerator;
use Laminaria\Conv\Operator;
use Howyi\Evi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Laminaria\Conv\Migration\Table\ViewCreateMigration;

class CheckViewCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('check:view');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $operator = $this->getOperator($input, $output);

        // $actualStructure = ViewStructureFactory::fromView(
        //     $this->getPDO('conv'),
        //     'conv',
        //     'view_user'
        // );
        // dump($actualStructure);
        $pdo = $this->getPDO('conv');

        $spec = Evi::parse('tests/Retort/check_schema/view_user.yml', Config::option('eval'));
        $expectStructure = ViewStructureFactory::fromSpec('view_user', $spec);
        // dump($expectStructure);
        dump((new ViewCreateMigration($expectStructure))->getUp());

        $pdo->exec('DROP VIEW IF EXISTS view_user');
        $pdo->exec((new ViewCreateMigration($expectStructure))->getUp());
        $viewQuery = $pdo->query('SHOW CREATE VIEW view_user')->fetch()['Create View'];

        $definer = ' DEFINER' . explode('DEFINER', $viewQuery)[1] . 'DEFINER';
        $viewQuery = str_replace($definer, '', $viewQuery);

        $up = (new ViewCreateMigration($expectStructure))->getUp();
        dump($up);
        dump($expectStructure->getCompareQuery());
        dump(str_replace([PHP_EOL, ' '], '', $viewQuery));
    }
}
