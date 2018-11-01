<?php

namespace Laminaria\Conv;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Laminaria\Conv\Structure\DatabaseStructure;
use Laminaria\Conv\Structure\TableStructureType;
use Laminaria\Conv\Structure\ViewRawStructure;
use Laminaria\Conv\Structure\TableStructure;
use Symfony\Component\Yaml\Yaml;
use Laminaria\Conv\Operator\OperatorInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class SchemaReflector
{
    /**
     * @param string            $path
     * @param DatabaseStructure $database
     * @param OperatorInterface $operator
     */
    public static function fromDatabaseStructure(
        string $path,
        DatabaseStructure $database,
        OperatorInterface $operator
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
        $operator->startProgress(count($database->getTableList()));
        $operator->setProgressFormat('debug');
        foreach ($database->getTableList() as $tableName => $structure) {
            switch ($structure->getType()) {
                case TableStructureType::TABLE:
                    self::fromTableStructure($path, $structure);
                    break;
                case TableStructureType::VIEW_RAW:
                    self::fromViewRawStructure($path, $structure);
                    break;
            }
            $operator->advanceProgress();
        }
        $operator->finishProgress("\nFinish");
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
            Yaml::dump($table->toArray(), 4, 2)
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
