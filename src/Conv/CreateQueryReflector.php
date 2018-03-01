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
use Symfony\Component\Console\Helper\ProgressBar;

class CreateQueryReflector
{
    /**
     * @param \PDO          $pdo
     * @param string        $dbName
     * @param string        $path
     * @param Operator      $operator
     * @param callable|null $filter
     */
    public static function fromPDO(
        \PDO $pdo,
        string $dbName,
        string $path,
        Operator $operator = null,
        callable $filter = null
    ) {
    	$dbs = DatabaseStructureFactory::fromPDO(
    	    $pdo,
	        $dbName,
	        $filter
	    );

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

	    // CREATE文の発行
	    $tables = $pdo->query(
		    "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = '$dbName'"
	    )->fetchAll();

	    $progress = null;

	    if (!is_null($operator)) {
		    $operator->output("\nSave generated queries to '$path'");
		    $progress = $operator->getProgress(count($tables));
		    $progress->start();
		    $progress->setFormat('debug');
	    }

	    $queries = [];
	    foreach ($dbs->getTableList() as $table) {
		    $name = $table->getName();
		    $showCreateTable = $pdo->query("SHOW CREATE TABLE {$name}")->fetch();
		    if (isset($showCreateTable['Create Table'])) {
			    $query = preg_replace('/ AUTO_INCREMENT=[0-9]+/i', '', $showCreateTable['Create Table']);
		    } else {
			    $query = preg_replace('/ DEFINER=.+ SQL SECURITY DEFINER/', '', $showCreateTable['Create View']);
			    $query = strtr($query, [
				    ' AS select ' => ' AS select' . PHP_EOL . '  ',
				    ',' => ',' . PHP_EOL . '  ',
				    ' from (' => PHP_EOL . 'from (',
			    ]);
		    }
		    $query .= "\n";

		    if (!is_null($operator)) {
			    $progress->advance();
		    }

		    $queries[$name] = $query;
	    }

	    // ファイルへ保存
	    foreach ($queries as $name => $query) {
		    file_put_contents(
			    sprintf('%s/%s.sql', $path, $name),
			    $query
		    );
	    }

        if (!is_null($operator)) {
            $progress->finish();
            $operator->output("\nFinish");
        }
    }
}
