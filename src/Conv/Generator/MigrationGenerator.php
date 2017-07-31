<?php

namespace Conv\Generator;

use Conv\Migration\Table\TableDropMigration;
use Conv\Migration\Table\TableCreateMigration;
use Conv\Migration\Database\Migration;
use Conv\Migration\Table\TableAlterMigration;
use Conv\Structure\DatabaseStructure;
use Conv\Util\Operator;

class MigrationGenerator
{
    /**
     * @param DatabaseStructure  $beforeDatabase
     * @param DatabaseStructure  $afterDatabase
     * @param Operator           $operator
     * @return Migration
     */
    public static function generate(
        DatabaseStructure $beforeDatabase,
        DatabaseStructure $afterDatabase,
        Operator $operator
    ): Migration {
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
                    'Select a renamed column.',
                    $addedTableNameList
                );
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
                $operator
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
                $operator
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
