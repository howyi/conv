<?php

namespace Laminaria\Conv\Util;

use Laminaria\Conv\Util\SchemaKey;
use Laminaria\Conv\Structure\TableStructureType;

class SchemaValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testValidate()
    {
        $array = [
            SchemaKey::TABLE_TYPE=> TableStructureType::TABLE,
            SchemaKey::TABLE_COMMENT=> 'Music management table',
            SchemaKey::TABLE_COLUMN => [
                'music_id' => [
                    SchemaKey::COLUMN_TYPE   => 'int(10)',
                    SchemaKey::COLUMN_COMMENT => 'Unique music ID',
                ],
                'name' => [
                    SchemaKey::COLUMN_TYPE   => 'int(10)',
                    SchemaKey::COLUMN_COMMENT => 'Music name',
                ],
            ],
            SchemaKey::TABLE_PRIMARY_KEY => [
                'music_id'
            ]
        ];
        SchemaValidator::validate('test', $array);
        // dummy
        $this->assertTrue(true);
    }

    /**
     * @dataProvider validateFailedProvider
     * @expectedException Laminaria\Conv\Util\SchemaValidateException
     */
    public function testValidateFailed($array)
    {
        // dummy
        $this->assertTrue(true);
        SchemaValidator::validate('test', $array);
    }

    public function validateFailedProvider()
    {
        return [
            [
                [
                    SchemaKey::TABLE_TYPE => TableStructureType::TABLE,
                    SchemaKey::TABLE_COMMENT => 'Music management table',
                    SchemaKey::TABLE_COLUMN => [
                        'music_id' => [
                            SchemaKey::COLUMN_COMMENT => 'Unique music ID',
                        ],
                        'name' => [
                            SchemaKey::COLUMN_TYPE   => 'int(10)',
                            SchemaKey::COLUMN_COMMENT => 'Music name',
                        ],
                    ],
                    SchemaKey::TABLE_PRIMARY_KEY => [
                        'music_id'
                    ]
                ]
            ],
            [
                [
                    SchemaKey::TABLE_COLUMN => [
                        'music_id' => [
                            SchemaKey::COLUMN_TYPE   => 'int(10)',
                            SchemaKey::COLUMN_COMMENT => 'Unique music ID',
                        ],
                        'name' => [
                            SchemaKey::COLUMN_TYPE   => 'int(10)',
                            SchemaKey::COLUMN_COMMENT => 'Music name',
                        ],
                    ],
                    SchemaKey::TABLE_PRIMARY_KEY => [
                        'music_id'
                    ]
                ]
            ],
            [
                [
                    SchemaKey::TABLE_TYPE => TableStructureType::TABLE,
                    SchemaKey::TABLE_COMMENT => 'Music management table',
                    SchemaKey::TABLE_COLUMN => [
                        'music_id' => [
                            SchemaKey::COLUMN_TYPE   => 'int(10)',
                            SchemaKey::COLUMN_COMMENT => 'Unique music ID',
                        ],
                        'name' => [
                            SchemaKey::COLUMN_TYPE   => 'int(10)',
                            SchemaKey::COLUMN_COMMENT => 'Music name',
                        ],
                    ],
                    SchemaKey::TABLE_PRIMARY_KEY => [
                        'music_id',
                        'music_id',
                    ]
                ]
            ],
            [
                [
                    SchemaKey::TABLE_TYPE => TableStructureType::TABLE,
                    SchemaKey::TABLE_COMMENT => 'Music management table',
                    SchemaKey::TABLE_COLUMN => [
                        'music_id' => [
                            SchemaKey::COLUMN_TYPE   => 'int(10)',
                            SchemaKey::COLUMN_COMMENT => 'Unique music ID',
                        ],
                        'name' => [
                            SchemaKey::COLUMN_TYPE   => 'int(10)',
                            SchemaKey::COLUMN_COMMENT => 'Music name',
                        ],
                    ],
                    SchemaKey::TABLE_INDEX => [
                        'music_id' => [
                            SchemaKey::INDEX_COLUMN => [
                                'music_id'
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    SchemaKey::TABLE_TYPE => TableStructureType::TABLE,
                    SchemaKey::TABLE_COMMENT => 'Music management table',
                    SchemaKey::TABLE_COLUMN => [
                        'music_id' => [
                            SchemaKey::COLUMN_TYPE   => 'int(10)',
                            SchemaKey::COLUMN_COMMENT => 'Unique music ID',
                        ],
                        'name' => [
                            SchemaKey::COLUMN_TYPE   => 'int(10)',
                            SchemaKey::COLUMN_COMMENT => 'Music name',
                        ],
                    ],
                    SchemaKey::TABLE_PRIMARY_KEY => [
                        'music_id'
                    ],
                    SchemaKey::TABLE_INDEX => [
                        'music_id' => [
                            SchemaKey::INDEX_TYPE => true,
                            SchemaKey::INDEX_COLUMN => [
                                'music_id',
                                'music_id',
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
