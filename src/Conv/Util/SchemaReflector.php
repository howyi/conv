<?php

namespace Conv\Util;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Conv\Structure\DatabaseStructure;
use Conv\Structure\TableStructure;
use Symfony\Component\Yaml\Yaml;

class SchemaReflector
{
    /**
     * @param QuestionHelper  $helper
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public static function fromDatabaseStructure(
        string $path,
        DatabaseStructure $database
    ) {
        if(!file_exists($path)){
            mkdir($path, 0777, true);
        }
        foreach ($database->getTableList() as $tableName => $tableStructure) {
            self::fromTableStructure($path, $tableStructure);
        }
    }

    /**
     * @param string $message
     * @param array  $choices
     * @return mixed
     */
    public static function fromTableStructure(
        string $path,
        TableStructure $table
    ) {
        file_put_contents(
            sprintf(
                '%s/%s.yml',
                $path,
                $table->getTableName()
            ),
            Yaml::dump($table->toArray(), 10, 2)
        );
    }
}
