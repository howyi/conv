<?php

namespace Conv;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Conv\Structure\DatabaseStructure;
use Conv\Structure\TableStructureType;
use Conv\Structure\ViewRawStructure;
use Conv\Structure\TableStructure;
use Symfony\Component\Yaml\Yaml;
use Conv\Operator;

class SchemaReflector
{
    /**
     * @param QuestionHelper  $helper
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Operator        $operator
     */
    public static function fromDatabaseStructure(
        string $path,
        DatabaseStructure $database,
        Operator $operator
    ) {
        if (file_exists($path)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path)
            );
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    unlink($fileinfo->getPathName());
                }
            }
        } else {
            mkdir($path, 0777, true);
        }
        $operator->output("\nGenerate schemas to '$path' directory");
        $progress = $operator->getProgress(count($database->getTableList()));
        $progress->start();
        $progress->setFormat('debug');
        foreach ($database->getTableList() as $tableName => $structure) {
            switch ($structure->getType()) {
                case TableStructureType::TABLE:
                    self::fromTableStructure($path, $structure);
                    break;
                case TableStructureType::VIEW_RAW:
                    self::fromViewRawStructure($path, $structure);
                    break;
            }
            $progress->advance();
        }
        $progress->finish();
        $operator->output("\nFinish");
    }

    /**
     * @param string         $path
     * @param TableStructure $table
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
            Yaml::dump($table->toArray(), 3, 2)
        );
    }

    /**
     * @param string           $path
     * @param ViewRawStructure $view
     */
    public static function fromViewRawStructure(
        string $path,
        ViewRawStructure $view
    ) {
        file_put_contents(
            sprintf(
                '%s/%s.yml',
                $path,
                $view->getViewName()
            ),
            Yaml::dump($view->toArray(), 3, 2)
        );
    }
}
