<?php

namespace Laminaria\Conv\Command;

use Laminaria\Conv\Migration\Database\Migration;
use Symfony\Component\Console\Command\Command;
use Laminaria\Conv\Operator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractConvCommand extends Command
{
    /**
     * 'user:pass@host:port' -> \PDO
     *
     * @param string      $server
     * @param string|null $dbName
     * @return \PDO
     */
    public function convertToPdo(string $server, ?string $dbName = null): \PDO
    {
        [$user, $address] = explode('@', $server);

        $explodedUser = explode(':', $user);
        $explodedAddress = explode(':', $address);

        $pdo = new \PDO(
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

        if (!is_null($dbName)) {
            $pdo->query("USE $dbName");
        }

        return $pdo;
    }

    public function getOperator(InputInterface $input, OutputInterface $output): Operator
    {
        return new Operator(
            $this->getHelper('question'),
            $input,
            $output
        );
    }

	public function displayAlterMigration(Migration $alterMigrations, Operator $operator): void
	{
		$operator->output("\n");

		if (0 !== count($alterMigrations->getMigrationList())) {

			foreach ($alterMigrations->getMigrationList() as $migration) {
				$operator->output("<fg=green>### TABLE NAME: {$migration->getTableName()}</>");
				$operator->output('<fg=yellow>--------- UP ---------</>');
				$operator->output("<fg=blue>{$migration->getUp()}</>");
				$operator->output('<fg=yellow>-------- DOWN --------</>');
				$operator->output("<fg=magenta>{$migration->getDown()}</>\n\n");
			}
		}

		$count = count($alterMigrations->getMigrationList());
		$operator->output("<fg=green>Generated $count migrations</>");
	}
}
