<?php

namespace Conv;

use Conv\Generator\TableAlterMigrationGenerator;
use Conv\Generator\ViewAlterMigrationGenerator;
use Conv\Migration\Database\Migration;
use Conv\Migration\Table\TableAlterMigration;
use Conv\Migration\Table\TableCreateMigration;
use Conv\Migration\Table\ViewCreateMigration;
use Conv\Migration\Table\TableDropMigration;
use Conv\Migration\Table\ViewDropMigration;
use Conv\Migration\Table\ViewAlterMigration;
use Conv\Migration\Table\ViewRenameMigration;
use Conv\Structure\DatabaseStructure;
use Conv\Operator;
use Conv\Structure\TableStructureType;

class MigrationGenerator
{
    /**
     * @param DatabaseStructure  $beforeDatabase
     * @param DatabaseStructure  $afterDatabase
     * @param Operator           $operator
     * @param bool               $forceDrop
     * @return Migration
     */
    public static function generate(
        DatabaseStructure $beforeDatabase,
        DatabaseStructure $afterDatabase,
        Operator $operator,
        bool $forceDrop = false
    ): Migration {
        // DROP → MODIFY → ADD の順でマイグレーションを生成する

        // テーブル、もしくはカラムの変更後配列
        $allRenamedNameList = [];

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

           if ($forceDrop) {
               $droppedTableNameList[] = $missingTableName;
               continue;
           }

            $answer = $operator->choiceQuestion(
                sprintf('Table %s is missing. Choose an action.', $missingTableName),
                ['dropped', sprintf('renamed (%s)', implode(', ', $addedTableNameList))]
            );
            if ('dropped' === $answer) {
                $droppedTableNameList[] = $missingTableName;
                continue;
            }

            if (1 === count($addedTableNameList)) {
                $renamedTableName = current($addedTableNameList);
            } else {
                $renamedTableName = $operator->choiceQuestion(
                    'Select a renamed table.',
                    $addedTableNameList
                );
            }
            $renamedTableNameList[$missingTableName] = $renamedTableName;
            $allRenamedNameList[] = [$renamedTableName];
            $addedTableNameList = array_diff($addedTableNameList, [$renamedTableName]);
        }

        // 全ての消滅したビュー配列
        $missingViewList = $beforeDatabase->getDiffViewList($afterDatabase);
        // 全ての追加したビュー配列
        $unknownViewList = $afterDatabase->getDiffViewList($beforeDatabase);

        // 削除したビュー名配列
        $droppedViewNameList = [];
        // 名称変更したビュー名配列 キー=変更前ビュー名
        $renamedViewNameList = [];
        // 追加したビュー名配列
        $addedViewNameList = array_keys($unknownViewList);

        foreach ($missingViewList as $missingViewName => $missingView) {
            if (0 === count($addedViewNameList)) {
                $droppedViewNameList[] = $missingViewName;
                continue;
            }

            if ($forceDrop) {
                $droppedViewNameList[] = $missingViewName;
                continue;
            }

            $answer = $operator->choiceQuestion(
                sprintf('View %s is missing. Choose an action.', $missingViewName),
                ['dropped', sprintf('renamed (%s)', implode(', ', $addedViewNameList))]
            );
            if ('dropped' === $answer) {
                $droppedViewNameList[] = $missingViewName;
                continue;
            }

            if (1 === count($addedViewNameList)) {
                $renamedViewName = current($addedViewNameList);
            } else {
                $renamedViewName = $operator->choiceQuestion(
                    'Select a renamed view.',
                    $addedViewNameList
                );
            }
            $renamedViewNameList[$missingViewName] = $renamedViewName;
            $addedViewNameList = array_diff($addedViewNameList, [$renamedViewName]);
        }

        $migration = new Migration();

        foreach ($droppedTableNameList as $tableName) {
            $migration->add(
                new TableDropMigration($beforeDatabase->getTableList()[$tableName])
            );
        }

        foreach ($droppedViewNameList as $viewName) {
            $migration->add(
                new ViewDropMigration($beforeDatabase->getTableList()[$viewName])
            );
        }

        $filter = [TableStructureType::TABLE];
        foreach ($beforeDatabase->getTableList($filter) as $tableName => $beforeTable) {
            if (!array_key_exists($tableName, $afterDatabase->getTableList())) {
                continue;
            }
            $tableAlterMigration = TableAlterMigrationGenerator::generate(
                $beforeTable,
                $afterDatabase->getTableList()[$tableName],
                $operator,
                $forceDrop
            );
            $allRenamedNameList = array_merge($allRenamedNameList, $tableAlterMigration->renamedNameList());
            if (!$tableAlterMigration->isAltered()) {
                continue;
            }
            $migration->add($tableAlterMigration);
        }

        foreach ($renamedTableNameList as $beforeTableName => $afterTableName) {
            $tableAlterMigration = TableAlterMigrationGenerator::generate(
                $beforeDatabase->getTableList()[$beforeTableName],
                $afterDatabase->getTableList()[$afterTableName],
                $operator,
                $forceDrop
            );
            $allRenamedNameList = array_merge($allRenamedNameList, $tableAlterMigration->renamedNameList());
            $migration->add($tableAlterMigration);
        }

        $filter = [TableStructureType::VIEW, TableStructureType::VIEW_RAW];
        foreach ($beforeDatabase->getTableList($filter) as $viewName => $beforeView) {
            if (!array_key_exists($viewName, $afterDatabase->getTableList())) {
                continue;
            }
            $viewAlterMigration = new ViewAlterMigration(
                $beforeView,
                $afterDatabase->getTableList()[$viewName],
                $allRenamedNameList
            );
            if (!$viewAlterMigration->isAltered()) {
                continue;
            }
            // if ($viewAlterMigration->isSplit()) {
            //     $migration->addSplit($viewAlterMigration);
            // } else {
            //     $migration->add($viewAlterMigration);
            // }
            $migration->add($viewAlterMigration);
        }

        foreach ($renamedViewNameList as $beforeViewName => $afterViewName) {
            $beforeView = $beforeDatabase->getTableList()[$beforeViewName];
            $afterView = $afterDatabase->getTableList()[$afterViewName];
            $migration->add(new ViewRenameMigration($beforeView, $afterView));

            $viewAlterMigration = new ViewAlterMigration($beforeView, $afterView, $allRenamedNameList);
            if (!$viewAlterMigration->isAltered()) {
                continue;
            }
            // if ($viewAlterMigration->isSplit()) {
            //     $migration->addSplit($viewAlterMigration);
            // } else {
            //     $migration->add($viewAlterMigration);
            // }
            $migration->add($viewAlterMigration);
        }

        foreach ($addedTableNameList as $tableName) {
            $migration->add(
                new TableCreateMigration($afterDatabase->getTableList()[$tableName])
            );
        }

        foreach ($addedViewNameList as $viewName) {
            $migration->add(
                new ViewCreateMigration($afterDatabase->getTableList()[$viewName])
            );
        }

        return $migration;
    }
}
