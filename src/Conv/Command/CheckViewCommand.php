<?php

namespace Conv\Command;

use Conv\Util\Config;
use Conv\Factory\ViewStructureFactory;
use Conv\MigrationGenerator;
use Conv\Generator\TableAlterMigrationGenerator;
use Conv\Operator;
use Howyi\Evi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Conv\Migration\Table\ViewCreateMigration;

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
        $spec = Evi::parse('schema/view_user.yml', Config::option('eval'));
        $expectStructure = ViewStructureFactory::fromSpec('view_user', $spec);
        // dump($expectStructure);
        dump($expectStructure->getJoinStructure()->genareteJoinQuery());
        dump(new ViewCreateMigration($expectStructure));
    }
}
