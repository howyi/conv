<?php

namespace Conv\Command;

use Conv\DatabaseStructureFactory;
use Conv\MigrationGenerator;
use Conv\Operator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiffCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('diff')
            ->addOption(
                'server1',
                null,
                InputArgument::OPTIONAL,
                'user:pass@host:port',
                'root@localhost'
            )
            ->addOption(
                'server2',
                null,
                InputArgument::OPTIONAL,
                'user:pass@host:port',
                'root@localhost'
            )
            ->addArgument(
                'dbNames',
                InputArgument::REQUIRED,
                'db1:db2'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        [$dbName1, $dbName2] = explode(':', $input->getArgument('dbNames'));

        $pdo1 = $this->convertPdo($input->getOption('server1'), $dbName1);
        $pdo2 = $this->convertPdo($input->getOption('server2'), $dbName2);

        $db1Structure = DatabaseStructureFactory::fromPDO($pdo1, $dbName1);
        $db2Structure = DatabaseStructureFactory::fromPDO($pdo2, $dbName2);

        $alterMigrations = MigrationGenerator::generate(
            $db1Structure,
            $db2Structure,
            $operator = new Operator($this->getHelper('question'), $input, $output)
        );

        $operator->output("\n\n\n\n");

        foreach ($alterMigrations->getMigrationList() as $migration) {
            $operator->output("<fg=green>### TABLE NAME: {$migration->getTableName()}</>");
            $operator->output('<fg=yellow>------ UP ------</>');
            $operator->output("<fg=blue>{$migration->getUp()}</>");
            $operator->output('<fg=yellow>------ DOWN ----</>');
            $operator->output("<fg=magenta>{$migration->getDown()}</>\n\n");
        }
    }

    private function convertPdo(string $server, string $dbName): \PDO
    {
        [$user, $address] = explode('@', $server);

        $explodedUser = explode(':', $user);
        $explodedAddress = explode(':', $address);

        return new \PDO(
            sprintf(
                'mysql:host=%s;port=%s;dbname=%s',
                $explodedAddress[0],
                ($explodedAddress[1] ?? '3306'),
                $dbName
            ),
            $explodedUser[0],
            $explodedUser[1] ?? '',
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
    }
}
