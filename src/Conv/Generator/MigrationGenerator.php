<?php

namespace Conv\Generator;

use Conv\Migration\Table\TableDropMigration;
use Conv\Migration\Table\TableCreateMigration;
use Conv\Migration\Database\Migration;
use Conv\Migration\Table\TableAlterMigration;
use Conv\Structure\DatabaseStructure;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class MigrationGenerator
{
    /**
     * @param DatabaseStructure  $beforeDatabase
     * @param DatabaseStructure  $afterDatabase
     * @param QuestionHelper  $helper
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return TableMigration
     */
    public static function generate(
        DatabaseStructure $beforeDatabase,
        DatabaseStructure $afterDatabase,
        QuestionHelper $helper,
        InputInterface $input,
        OutputInterface $output
    ) {
        // DROP → MODIFY → ADD の順でマイグレーションを生成する

        // 全ての消滅したテーブル配列
        $missingTableList = $beforeDatabase->getDiffTableList($afterDatabase);
        // 全ての追加したテーブル配列
        $unknownTableList = $afterDatabase->getDiffTableList($beforeDatabase);

        // 削除したテーブル名配列
        $droppedTableNameList = [];
        // 名称変更したテーブル名配列 キー=変更前テーブル名
        $renamedTableNameList = [];
        // 追加したテーブル名配列
        $addedTableNameList = array_keys($unknownTableList);

        foreach ($missingTableList as $missingTableName => $missingTable) {
            if (0 === count($addedTableNameList)) {
                $droppedTableNameList[] = $missingTableName;
                continue;
            }

            $question = new ChoiceQuestion(
                sprintf('Table %s is missing. Choose an action.', $missingTableName),
                ['dropped', sprintf('renamed (%s)', implode(', ', $addedTableNameList))]
            );
            if ('dropped' === $helper->ask($input, $output, $question)) {
                $droppedTableNameList[] = $missingTableName;
                continue;
            }

            if (1 === count($addedTableNameList)) {
                $renamedTableName = current($addedTableNameList);
            } else {
                $question = new ChoiceQuestion(
                    'Select a renamed column.',
                    $addedTableNameList
                );
                $renamedTableName = $helper->ask($input, $output, $question);
            }
            $renamedTableNameList[$missingTableName] = $renamedTableName;
            $addedTableNameList = array_diff($addedTableNameList, [$renamedTableName]);
        }

        $migration = new Migration();

        foreach ($droppedTableNameList as $tableName) {
            $migration->add(
                new TableDropMigration($beforeDatabase->getTableList()[$tableName])
            );
        }

        foreach ($beforeDatabase->getTableList() as $tableName => $beforeTable) {
            if (!array_key_exists($tableName, $afterDatabase->getTableList())) {
                continue;
            }
            $tableAlterMigration = TableAlterMigrationGenerator::generate(
                $beforeTable,
                $afterDatabase->getTableList()[$tableName],
                $helper,
                $input,
                $output
            );
            if (!$tableAlterMigration->isAltered()) {
                continue;
            }
            $migration->add($tableAlterMigration);
        }

        foreach ($renamedTableNameList as $beforeTableName => $afterTableName) {
            $tableAlterMigration = TableAlterMigrationGenerator::generate(
                $beforeDatabase->getTableList()[$beforeTableName],
                $afterDatabase->getTableList()[$afterTableName],
                $helper,
                $input,
                $output
            );
            $migration->add($tableAlterMigration);
        }

        foreach ($addedTableNameList as $tableName) {
            $migration->add(
                new TableCreateMigration($afterDatabase->getTableList()[$tableName])
            );
        }

        return $migration;
    }
}
