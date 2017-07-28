<?php

namespace Conv\Generator;

use Conv\Migration\Line\ColumnAddMigrationLine;
use Conv\Migration\Line\ColumnDropMigrationLine;
use Conv\Migration\Line\ColumnModifyMigrationLine;
use Conv\Migration\Line\IndexModifyMigrationLine;
use Conv\Migration\Line\TableRenameMigrationLine;
use Conv\Migration\Table\MigrationLineList;
use Conv\Migration\Table\TableAlterMigration;
use Conv\Structure\TableStructure;
use Conv\Util\FieldOrder;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class TableAlterMigrationGenerator
{
    /**
     * @param TableStructure  $beforeTable
     * @param TableStructure  $afterTable
     * @param QuestionHelper  $helper
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return TableMigration
     */
    public static function generate(
        TableStructure $beforeTable,
        TableStructure $afterTable,
        QuestionHelper $helper,
        InputInterface $input,
        OutputInterface $output
    ) {
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

            $question = new ChoiceQuestion(
                sprintf('Column %s.%s is missing. Choose an action.', $beforeTable->tableName, $missingField),
                ['dropped', sprintf('renamed (%s)', implode(', ', $addedFieldList))]
            );
            if ('dropped' === $helper->ask($input, $output, $question)) {
                $droppedFieldList[] = $missingField;
                continue;
            }

            if (1 === count($addedFieldList)) {
                $renamedField = current($addedFieldList);
            } else {
                $question = new ChoiceQuestion(
                    'Select a renamed column.',
                    $addedFieldList
                );
                $renamedField = $helper->ask($input, $output, $question);
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

        $indexAllMigration = IndexMigrationGenerator::generate($beforeTable, $afterTable);

        $migrationLineList = new MigrationLineList();
        if ($beforeTable->getTableName() !== $afterTable->getTableName()) {
            $migrationLineList->add(
                new TableRenameMigrationLine($beforeTable->getTableName(), $afterTable->getTableName())
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
            $output->writeln(sprintf('<info>TableName</> : %s', $displayTableName));

            foreach ($droppedModifiedColumnList as $modifiedColumn) {
                $output->writeln(sprintf('    <fg=red>dropped:   %s</>', $modifiedColumn->getField()));
            }

            foreach ($modifiedColumnSetList as $modifiedColumnSet) {
                $modifiedColumn = $modifiedColumnSet->getUpColumn();
                if ($modifiedColumn->isRenamed()) {
                    $displayColumn = sprintf('%s -> %s', $modifiedColumn->getBeforeField(), $modifiedColumn->getField());
                } else {
                    $displayColumn = $modifiedColumn->getField();
                }
                $output->writeln(sprintf('    <fg=green>modified:  %s</>',$displayColumn));
            }

            foreach ($addedModifiedColumnList as $modifiedColumn) {
                $output->writeln(sprintf('    <fg=cyan>added:     %s</>', $modifiedColumn->getField()));
            }
        }

        return $tableAlterMigration;
    }
}
