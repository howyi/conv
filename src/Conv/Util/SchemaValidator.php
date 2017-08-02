<?php

namespace Conv\Util;

use Conv\Util\SchemaKey;
use Conv\Structure\Attribute;

class SchemaValidator
{
    /**
     * @param string $path
     * @param array  $spec
     * @throws SchemaValidateException
     */
    public static function validate(string $path, array $spec)
    {
        if (0 !== count(array_diff(SchemaKey::TABLE_REQUIRE_KEYS, array_keys($spec)))) {
            throw new SchemaValidateException(
                sprintf(
                    $path . PHP_EOL . 'Table require key (%s) does not exist',
                    implode(
                        ', ',
                        array_diff(SchemaKey::TABLE_REQUIRE_KEYS, array_keys($spec))
                    )
                )
            );
        }

        foreach ($spec[SchemaKey::TABLE_COLUMN] as $key => $value) {
            if (0 !== count(array_diff(SchemaKey::COLUMN_REQUIRE_KEYS, array_keys($value)))) {
                throw new SchemaValidateException(
                    sprintf(
                        $path . PHP_EOL .'Column %s require key (%s) does not exist',
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

            if (0 !== count(array_diff($pkList, array_keys($spec[SchemaKey::TABLE_COLUMN])))) {
                throw new SchemaValidateException(
                    sprintf(
                        $path . PHP_EOL .'Primary key (%s) is not column',
                        implode(
                            ', ',
                            array_diff($pkList, array_keys($spec[SchemaKey::TABLE_COLUMN]))
                        )
                    )
                );
            }

            if (count($pkList) !== count(array_unique($pkList))) {
                throw new SchemaValidateException(
                    sprintf(
                        $path . PHP_EOL .'Duplicate primary_key (%s)',
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
                            $path . PHP_EOL .'Index %s require key (%s) does not exist',
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
                            $path . PHP_EOL .'Duplicate index (%s)',
                            implode(', ', $value[SchemaKey::INDEX_COLUMN])
                        )
                    );
                }

                if (0 !== count(array_diff($value[SchemaKey::INDEX_COLUMN], array_keys($spec[SchemaKey::TABLE_COLUMN])))) {
                    throw new SchemaValidateException(
                        sprintf(
                            $path . PHP_EOL .'Index %s (%s) is not column',
                            $key,
                            implode(
                                ', ',
                                array_diff($value[SchemaKey::INDEX_COLUMN], array_keys($spec[SchemaKey::TABLE_COLUMN]))
                            )
                        )
                    );
                }
            }
        }
    }
}
