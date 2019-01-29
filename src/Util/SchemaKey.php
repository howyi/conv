<?php

namespace Howyi\Conv\Util;

class SchemaKey
{
    const TABLE_TYPE            = 'type';
    const TABLE_COMMENT         = 'comment';
    const TABLE_COLUMN          = 'column';
    const TABLE_PRIMARY_KEY     = 'primary_key';
    const TABLE_INDEX           = 'index';
    const TABLE_ENGINE          = 'engine';
    const TABLE_DEFAULT_CHARSET = 'default_charset';
    const TABLE_COLLATE         = 'collate';
    const TABLE_PARTITION       = 'partition';

    const TABLE_KEYS = [
        self::TABLE_TYPE,
        self::TABLE_COMMENT,
        self::TABLE_COLUMN,
        self::TABLE_PRIMARY_KEY,
        self::TABLE_INDEX,
        self::TABLE_ENGINE,
        self::TABLE_DEFAULT_CHARSET,
        self::TABLE_COLLATE,
        self::TABLE_PARTITION,
    ];

    const TABLE_REQUIRE_KEYS = [
        self::TABLE_TYPE,
        self::TABLE_COLUMN,
    ];

    const TABLE_OPTIONAL_KEYS = [
        self::TABLE_COMMENT,
        self::TABLE_PRIMARY_KEY,
        self::TABLE_INDEX,
        self::TABLE_ENGINE,
        self::TABLE_DEFAULT_CHARSET,
        self::TABLE_COLLATE,
        self::TABLE_PARTITION,
    ];

    const COLUMN_TYPE      = 'type';
    const COLUMN_DEFAULT   = 'default';
    const COLUMN_COMMENT   = 'comment';
    const COLUMN_ATTRIBUTE = 'attribute';

    const COLUMN_KEYS = [
        self::COLUMN_TYPE,
        self::COLUMN_DEFAULT,
        self::COLUMN_COMMENT,
        self::COLUMN_ATTRIBUTE,
    ];

    const COLUMN_REQUIRE_KEYS = [
        self::COLUMN_TYPE,
    ];

    const COLUMN_OPTIONAL_KEYS = [
        self::COLUMN_DEFAULT,
        self::COLUMN_COMMENT,
        self::COLUMN_ATTRIBUTE,
    ];

    const INDEX_TYPE   = 'is_unique';
    const INDEX_COLUMN = 'column';

    const INDEX_REQUIRE_KEYS = [
        self::INDEX_TYPE,
        self::INDEX_COLUMN,
    ];

    const PARTITION_BY           = 'by';
    const PARTITION_VALUE        = 'value';
    const PARTITION_LIST         = 'list';
    const PARTITION_LESS_THAN    = 'less_than';
    const PARTITION_IN           = 'in';
    const PARTITION_ENGINE       = 'engine';
    const PARTITION_PART_COMMENT = 'comment';
    const PARTITION_NUM          = 'num';

    const VIEW_ALGORITHM = 'algorithm';
    const VIEW_ALIAS     = 'alias';
    const VIEW_COLUMN    = 'column';
    const VIEW_FROM      = 'from';

    const VIEW_KEYS = [
        self::VIEW_COLUMN,
        self::VIEW_FROM,
        self::VIEW_ALIAS,
    ];

    const VIEW_REQUIRE_KEYS = [
        self::VIEW_COLUMN,
        self::VIEW_FROM,
    ];

    const VIEW_OPTIONAL_KEYS = [
        self::VIEW_ALIAS,
    ];

    const JOIN_REFERENCE = 'reference';
    const JOIN_JOINS     = 'joins';
    const JOIN_FACTOR    = 'factor';
    const JOIN_ON        = 'on';

    const VIEW_RAW_QUERY = 'query';
}
