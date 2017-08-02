<?php

namespace Conv\Util;
use Conv\Util\SchemaKey;

class SchemaValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testValidate()
    {
        $array = [
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
     * @expectedException Conv\Util\SchemaValidateException
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
                    SchemaKey::TABLE_COMMENT=> 'Music management table',
                    SchemaKey::TABLE_COLUMN => [
                        'music_id' => [
                            SchemaKey::COLUMN_COMMENT=> 'Unique music ID',
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
                        'hello'
                    ]
                ]
            ],
            [
                [
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
                        'music_id',
                        'music_id',
                    ]
                ]
            ],
            [
                [
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
            [
                [
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
                    SchemaKey::TABLE_INDEX => [
                        'music_id' => [
                            SchemaKey::INDEX_TYPE => true,
                            SchemaKey::INDEX_COLUMN => [
                                'hello',
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
