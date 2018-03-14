<?php

namespace Conv\Command;

use Symfony\Component\Console\Command\Command;
use Conv\Operator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractConvCommand extends Command
{
    /**
     * 'user:pass@host:port' -> \PDO
     *
     * @param string $server
     * @param string $dbName
     * @return \PDO
     */
    public function convertToPdo(string $server, string $dbName): \PDO
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

    public function getOperator(InputInterface $input, OutputInterface $output): Operator
    {
        return new Operator(
            $this->getHelper('question'),
            $input,
            $output
        );
    }
}
