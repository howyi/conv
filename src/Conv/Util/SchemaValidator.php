<?php

namespace Conv\Util;

use Conv\Structure\Attribute;
use Conv\Structure\TableStructureType;
use Conv\Util\SchemaKey;

class SchemaValidator
{
    /**
     * @param string $name
     * @param array  $spec
     * @throws SchemaValidateException
     */
    public static function validate(string $name, array $spec)
    {
        if (isset($spec[SchemaKey::TABLE_TYPE]) and $spec[SchemaKey::TABLE_TYPE] === TableStructureType::VIEW_RAW) {
            return;
        }

        if (0 !== count(array_diff(SchemaKey::TABLE_REQUIRE_KEYS, array_keys($spec)))) {
            throw new SchemaValidateException(
                sprintf(
                    $name . PHP_EOL . 'Table require key (%s) does not exist',
                    implode(
                        ', ',
                        array_diff(SchemaKey::TABLE_REQUIRE_KEYS, array_keys($spec))
                    )
                )
            );
        }

        switch ($spec[SchemaKey::TABLE_TYPE]) {
            case TableStructureType::TABLE:
                self::validateTableSpec($name, $spec);
                break;
            case TableStructureType::VIEW:
                self::validateViewSpec($name, $spec);
                break;
        }
    }

    /**
     * @param string $name
     * @param array  $spec
     * @throws SchemaValidateException
     */
    private static function validateTableSpec(string $name, array $spec)
    {
        foreach ($spec[SchemaKey::TABLE_COLUMN] as $key => $value) {
            if (0 !== count(array_diff(SchemaKey::COLUMN_REQUIRE_KEYS, array_keys($value)))) {
                throw new SchemaValidateException(
                    sprintf(
                        $name . PHP_EOL .'Column %s require key (%s) does not exist',
                        $key,
                        implode(
                            ', ',
                            array_diff(SchemaKey::COLUMN_REQUIRE_KEYS, array_keys($value))
                        )
                    )
                );
            }

            if (!array_key_exists(SchemaKey::COLUMN_ATTRIBUTE, $value)) {
                continue;
            }

            // TODO type validate
        }

        if (array_key_exists(SchemaKey::TABLE_PRIMARY_KEY, $spec)) {
            $pkList = $spec[SchemaKey::TABLE_PRIMARY_KEY];

            if (count($pkList) !== count(array_unique($pkList))) {
                throw new SchemaValidateException(
                    sprintf(
                        $name . PHP_EOL .'Duplicate primary_key (%s)',
                        implode(', ', $pkList)
                    )
                );
            }
        }

        if (array_key_exists(SchemaKey::TABLE_INDEX, $spec)) {
            foreach ($spec[SchemaKey::TABLE_INDEX] as $key => $value) {
                if (0 !== count(array_diff(SchemaKey::INDEX_REQUIRE_KEYS, array_keys($value)))) {
                    throw new SchemaValidateException(
                        sprintf(
                            $name . PHP_EOL .'Index %s require key (%s) does not exist',
                            $key,
                            implode(
                                ', ',
                                array_diff(SchemaKey::INDEX_REQUIRE_KEYS, array_keys($value))
                            )
                        )
                    );
                }

                if (count($value[SchemaKey::INDEX_COLUMN]) !== count(array_unique($value[SchemaKey::INDEX_COLUMN]))) {
                    throw new SchemaValidateException(
                        sprintf(
                            $name . PHP_EOL .'Duplicate index (%s)',
                            implode(', ', $value[SchemaKey::INDEX_COLUMN])
                        )
                    );
                }
            }
        }
    }

    /**
     * @param string $name
     * @param array  $spec
     * @throws SchemaValidateException
     */
    private static function validateViewSpec(string $name, array $spec)
    {
        // TODO
    }
}
