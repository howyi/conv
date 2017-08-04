<?php

namespace Conv\Generator;

use Conv\Migration\Line\ColumnAddMigrationLine;
use Conv\Migration\Line\ColumnDropMigrationLine;
use Conv\Migration\Line\ColumnModifyMigrationLine;
use Conv\Migration\Line\IndexModifyMigrationLine;
use Conv\Migration\Line\TableRenameMigrationLine;
use Conv\Migration\Line\TableCommentMigrationLine;
use Conv\Migration\Line\TableEngineMigrationLine;
use Conv\Migration\Line\TableCollateMigrationLine;
use Conv\Migration\Line\TableDefaultCharsetMigrationLine;
use Conv\Migration\Table\MigrationLineList;
use Conv\Migration\Table\TableAlterMigration;
use Conv\Structure\TableStructure;
use Conv\Structure\ModifiedColumnStructure;
use Conv\Structure\ModifiedColumnStructureSet;
use Conv\Util\FieldOrder;
use Conv\Util\Operator;

class TableAlterMigrationGenerator
{
    /**
     * @param TableStructure  $beforeTable
     * @param TableStructure  $afterTable
     * @param Operator        $operator
     * @return TableMigration
     */
    public static function generate(
        TableStructure $beforeTable,
        TableStructure $afterTable,
        Operator $operator
    ): TableAlterMigration {
        // DROP-INDEX → DROP → MODIFY → ADD → ADD-INDEX の順でマイグレーションを生成する

        // 全ての消滅したカラム配列
        $missingColumnList = $beforeTable->getDiffColumnList($afterTable);
        // 全ての追加したカラム配列
        $unknownColumnList = $afterTable->getDiffColumnList($beforeTable);

        // 削除したカラム名配列
        $droppedFieldList = [];
        // 名称変更したカラム名配列 キー=変更前カラム名
        $renamedFieldList = [];
        // 追加したカラム名配列
        $addedFieldList = array_keys($unknownColumnList);

        foreach ($missingColumnList as $missingField => $missingColumn) {
            if (0 === count($addedFieldList)) {
                $droppedFieldList[] = $missingField;
                continue;
            }

            $answer = $operator->choiceQuestion(
                sprintf('Column %s.%s is missing. Choose an action.', $beforeTable->tableName, $missingField),
                ['dropped', sprintf('renamed (%s)', implode(', ', $addedFieldList))]
            );
            if ('dropped' === $answer) {
                $droppedFieldList[] = $missingField;
                continue;
            }

            if (1 === count($addedFieldList)) {
                $renamedField = current($addedFieldList);
            } else {
                $renamedField = $operator->choiceQuestion(
                    'Select a renamed column.',
                    $addedFieldList
                );
            }
            $renamedFieldList[$missingField] = $renamedField;
            $addedFieldList = array_diff($addedFieldList, [$renamedField]);
        }
        $droppedModifiedColumnList = $beforeTable->getModifiedColumnList($droppedFieldList);
        $addedModifiedColumnList = $afterTable->getModifiedColumnList($addedFieldList);

        $modifiedColumnSetList = $beforeTable->generateModifiedColumnList($afterTable, $renamedFieldList);

        $beforeOrderFieldList = $beforeTable->getOrderFieldList($droppedFieldList);
        $afterOrderFieldList = $afterTable->getOrderFieldList($addedFieldList, array_flip($renamedFieldList));
        $movedFieldOrderList = FieldOrderGenerator::generate($beforeOrderFieldList, $afterOrderFieldList);

        foreach ($movedFieldOrderList as $beforeField => $fieldOrder) {
            if (array_key_exists($beforeField, $modifiedColumnSetList)) {
                $upModifiedColumn = $modifiedColumnSetList[$beforeField]->getUpColumn();
                $downModifiedColumn = $modifiedColumnSetList[$beforeField]->getDownColumn();
            } else {
                $upColumn = $afterTable->getFieldList()[$beforeField];
                $downColumn = $beforeTable->getFieldList()[$beforeField];
                $modifiedColumnSetList[$beforeField] =  new ModifiedColumnStructureSet(
                    $upModifiedColumn = new ModifiedColumnStructure($beforeField, $upColumn),
                    $downModifiedColumn = new ModifiedColumnStructure($beforeField, $downColumn)
                );
            }
            $upModifiedColumn->setModifiedAfter($fieldOrder->getNextAfterField());
            $downModifiedColumn->setModifiedAfter($fieldOrder->getPreviousAfterField());
        }

        // Generate Migrations

        $indexAllMigration = IndexMigrationGenerator::generate(
            $beforeTable->getIndexList(),
            $afterTable->getIndexList()
        );

        $migrationLineList = new MigrationLineList();
        if ($beforeTable->getTableName() !== $afterTable->getTableName()) {
            $migrationLineList->add(
                new TableRenameMigrationLine($beforeTable->getTableName(), $afterTable->getTableName())
            );
        }
        if ($beforeTable->getComment() !== $afterTable->getComment()) {
            $migrationLineList->add(
                new TableCommentMigrationLine($beforeTable->getComment(), $afterTable->getComment())
            );
        }
        if ($beforeTable->getEngine() !== $afterTable->getEngine()) {
            $migrationLineList->add(
                new TableEngineMigrationLine($beforeTable->getEngine(), $afterTable->getEngine())
            );
        }
        if ($beforeTable->getDefaultCharset() !== $afterTable->getDefaultCharset()) {
            $migrationLineList->add(
                new TableDefaultCharsetMigrationLine($beforeTable->getDefaultCharset(), $afterTable->getDefaultCharset())
            );
        }
        if ($beforeTable->getCollate() !== $afterTable->getCollate()) {
            $migrationLineList->add(
                new TableCollateMigrationLine($beforeTable->getCollate(), $afterTable->getCollate())
            );
        }
        if ($indexAllMigration->isFirstExist()) {
            $migrationLineList->add(
                $indexAllMigration->getFirst()
            );
        }
        if (0 !== count($droppedModifiedColumnList)) {
            $migrationLineList->add(
                new ColumnDropMigrationLine($droppedModifiedColumnList)
            );
        }
        if (0 !== count($modifiedColumnSetList)) {
            $migrationLineList->add(
                new ColumnModifyMigrationLine($modifiedColumnSetList)
            );
        }
        if (0 !== count($addedModifiedColumnList)) {
            $migrationLineList->add(
                new ColumnAddMigrationLine($addedModifiedColumnList)
            );
        }
        if ($indexAllMigration->isLastExist()) {
            $migrationLineList->add(
                $indexAllMigration->getLast()
            );
        }

        $tableAlterMigration = new TableAlterMigration($beforeTable->getTableName(), $afterTable->getTableName(), $migrationLineList);

        if ($tableAlterMigration->isAltered()) {
            // Display

            if ($beforeTable->getTableName() !== $afterTable->getTableName()) {
                $displayTableName = sprintf('%s -> %s', $beforeTable->getTableName(), $afterTable->getTableName());
            } else {
                $displayTableName = $afterTable->getTableName();
            }
            $operator->output(sprintf('<info>TableName</> : %s', $displayTableName));

            if ($beforeTable->getComment() !== $afterTable->getComment()) {
                $operator->output(sprintf('    <fg=cyan>Comment</> : %s -> %s', $beforeTable->getComment(), $afterTable->getComment()));
            }

            foreach ($droppedModifiedColumnList as $modifiedColumn) {
                $operator->output(sprintf('    <fg=red>dropped:   %s</>', $modifiedColumn->getField()));
            }

            foreach ($modifiedColumnSetList as $modifiedColumnSet) {
                $modifiedColumn = $modifiedColumnSet->getUpColumn();
                if ($modifiedColumn->isRenamed()) {
                    $displayColumn = sprintf('%s -> %s', $modifiedColumn->getBeforeField(), $modifiedColumn->getField());
                } else {
                    $displayColumn = $modifiedColumn->getField();
                }
                $operator->output(sprintf('    <fg=green>modified:  %s</>', $displayColumn));
            }

            foreach ($addedModifiedColumnList as $modifiedColumn) {
                $operator->output(sprintf('    <fg=cyan>added:     %s</>', $modifiedColumn->getField()));
            }
        }

        return $tableAlterMigration;
    }
}
