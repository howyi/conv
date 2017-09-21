<?php

namespace Conv\Migration\Table;

use Conv\MigrationType;
use Conv\Structure\ViewStructure;

/**
 * CREATE VIEW
 */
class ViewCreateMigration extends AbstractTableMigration
{
    /**
     * @param ViewStructure $viewStructure
     */
    public function __construct(
        ViewStructure $viewStructure
    ) {
        $this->tableName = $viewStructure->getViewName();
        $this->type = MigrationType::CREATE;

        $this->addLine("CREATE VIEW `$this->tableName`");
        $this->addLine('AS select');

        $bodyList = [];
        foreach ($viewStructure->getColumnList() as $field => $value) {
            $targetTableName = strstr($value, '.', true);
            $targetColumn = ltrim(strstr($value, '.', false), '.');
            $bodyList[] = "`$targetTableName`.`$targetColumn` AS `$field`";
        }
        $this->up .= "  ".join(',' . PHP_EOL . '  ', $bodyList) . PHP_EOL;
        $this->addLine('from');
        $this->addLine('  ' . $viewStructure->getJoinStructure()->genareteJoinQuery() . ';');

        $this->down = "DROP VIEW `$this->tableName`;";
    }

    /**
     * @param string $text
     */
    private function addLine(string $text)
    {
        $this->up .= $text . PHP_EOL;
    }
}
