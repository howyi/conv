<?php

namespace Conv\Factory;

use Conv\Structure\ViewStructure;
use Symfony\Component\Yaml\Yaml;
use Conv\Util\SchemaKey;
use Conv\Util\Evaluator;
use Conv\Config;
use Conv\Util\SchemaValidator;

class ViewStructureFactory
{
    /**
     * @param string $viewName
     * @param array  $spec
     * @return ViewStructure
     */
    public static function fromSpec(string $viewName, array $spec): ViewStructure
    {
        $properties = array_diff_key($spec, array_flip(SchemaKey::VIEW_KEYS));

        return new ViewStructure(
            $viewName,
            $spec[SchemaKey::VIEW_ALIAS],
            $spec[SchemaKey::VIEW_COLUMN],
            $spec[SchemaKey::VIEW_FROM],
            $properties
        );
    }

    /**
     * @param \PDO   $pdo
     * @param string $dbName
     * @param string $viewName
     * @return ViewStructure
     */
    public static function fromView(\PDO $pdo, string $dbName, string $viewName): ViewStructure
    {
        $rawViewStatus = $pdo->query(
            sprintf(
                "SELECT * FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = '%s'",
                $dbName,
                $viewName
            )
        )->fetch();
        $rawQuery = $rawViewStatus['VIEW_DEFINITION'];

        $escMask = 'ESCAPED_%d_';
        preg_match_all("/`(.*?)`/", $rawQuery, $escapedArray);
        $escapedArray = $escapedArray[0];
        $escArray = [];
        foreach ($escapedArray as $key => $field) {
            $escArray[sprintf($escMask, $key)] = str_replace('`', '', $field);
            $rawQuery = str_replace("$field", sprintf($escMask, $key), $rawQuery);
        }

        $parsed = explode(' from ', $rawQuery);
        $selectRows = explode(",",  trim(str_replace('select', '', $parsed[0])));
        $escColumns = [];
        foreach ($selectRows as $row) {
            $rowArray = explode('AS', $row);
            if (!isset($rowArray[1])) {
                $field = ltrim(strstr($rowArray[0], '.', false), '.');
            } else {
                $field = $rowArray[1];
            }
            $field = trim($field);
            $target = $rowArray[0];
            $escColumns[$field] = $target;
        }

        $joinSep = 'J_';
        $sepMask = [
            'left join'  => $joinSep . 'L_',
            'right join' => $joinSep . 'R_',
            'cross join' => $joinSep . 'C_',
            'join'       => $joinSep . 'N_',
        ];
        $joinRaw = str_replace(array_keys($sepMask), array_values($sepMask), $parsed[1]);
        $joins = explode($joinSep, $joinRaw);
        $escJoins = [];
        $escAliases = [];
        foreach ($joins as $row) {
            $rowArray = explode('on', $row);
            $parsedAlias = explode(' ', rtrim(ltrim(trim($rowArray[0]), '('), ')'));
            $attribute = '';
            if (3 === count($parsedAlias)) {
                $attribute = $parsedAlias[0];
                $parsedAlias[0] = $parsedAlias[1];
                $parsedAlias[1] = $parsedAlias[2];
            }
            if (isset($parsedAlias[1])) {
                $escAliases[ltrim(strstr($parsedAlias[0], '.', false), '.')] = $parsedAlias[1];
            }
            if (!isset($rowArray[1])) {
                continue;
            }
            $on = rtrim(ltrim(trim($rowArray[1]), '('), ')');
            $escJoins[] = [
                $attribute => $on
            ];
        }

        $columns = [];
        foreach ($escColumns as $key => $value) {
            $columns[$escArray[$key]] = str_replace(array_keys($escArray), array_values($escArray), $value);
        }

        $joins = [];
        foreach ($escJoins as $values) {
            foreach ($values as $key => $value) {
                $attribute = str_replace( ' ', '_', array_flip($sepMask)[$joinSep . $key]);
                $joins[] = [
                    $attribute => str_replace(array_keys($escArray), array_values($escArray), $value)
                ];
            }
        }

        $aliases = [];
        foreach ($escAliases as $key => $value) {
            $aliases[$escArray[$key]] = $escArray[$value];
        }

        return new ViewStructure(
            $viewName,
            $aliases,
            $columns,
            $joins,
            []
        );
    }
}
