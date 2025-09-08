<?php

namespace Howyi\Conv;

use Howyi\Conv\Operator\OperatorInterface;

class CreateQueryReflector
{
    /**
     * @param \PDO              $pdo
     * @param string            $dbName
     * @param string            $path
     * @param OperatorInterface $operator
     * @param callable|null $filter
     */
    public static function fromPDO(
        \PDO $pdo,
        string $dbName,
        string $path,
        OperatorInterface $operator,
        ?callable $filter = null
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

        $operator->output("\nSave generated queries to '$path'");
        $operator->startProgress(count($tables));
        $operator->setProgressFormat('debug');

        $queries = [];
        foreach ($dbs->getTableList() as $table) {
            $name = $table->getName();
            $showCreateTable = $pdo->query("SHOW CREATE TABLE {$name}")->fetch();
            if (isset($showCreateTable['Create Table'])) {
                $query = preg_replace('/ AUTO_INCREMENT=[0-9]+/i', '', $showCreateTable['Create Table']);
            } else {
                $query = preg_replace('/ DEFINER=.+ SQL SECURITY DEFINER/', '', (string) $showCreateTable['Create View']);
                $query = strtr($query, [
                    ' AS select ' => ' AS select' . PHP_EOL . '  ',
                    ',' => ',' . PHP_EOL . '  ',
                    ' from (' => PHP_EOL . 'from (',
                ]);
            }
            $query .= "\n";

            $operator->advanceProgress();

            $queries[$name] = $query;
        }

        // ファイルへ保存
        foreach ($queries as $name => $query) {
            file_put_contents(
                sprintf('%s/%s.sql', $path, $name),
                $query
            );
        }

        $operator->finishProgress("\nFinish");
    }
}
