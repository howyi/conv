<?php

namespace Howyi\Conv\Util;

class SchemaKey
{
    public const TABLE_TYPE            = 'type';
    public const TABLE_COMMENT         = 'comment';
    public const TABLE_COLUMN          = 'column';
    public const TABLE_PRIMARY_KEY     = 'primary_key';
    public const TABLE_INDEX           = 'index';
    public const TABLE_ENGINE          = 'engine';
    public const TABLE_DEFAULT_CHARSET = 'default_charset';
    public const TABLE_COLLATE         = 'collate';
    public const TABLE_PARTITION       = 'partition';
    public const TABLE_AUTO_RANDOM     = 'auto_random';

    public const TABLE_KEYS = [
        self::TABLE_TYPE,
        self::TABLE_COMMENT,
        self::TABLE_COLUMN,
        self::TABLE_PRIMARY_KEY,
        self::TABLE_INDEX,
        self::TABLE_ENGINE,
        self::TABLE_DEFAULT_CHARSET,
        self::TABLE_COLLATE,
        self::TABLE_PARTITION,
        self::TABLE_AUTO_RANDOM,
    ];

    public const TABLE_REQUIRE_KEYS = [
        self::TABLE_TYPE,
        self::TABLE_COLUMN,
    ];

    public const TABLE_OPTIONAL_KEYS = [
        self::TABLE_COMMENT,
        self::TABLE_PRIMARY_KEY,
        self::TABLE_INDEX,
        self::TABLE_ENGINE,
        self::TABLE_DEFAULT_CHARSET,
        self::TABLE_COLLATE,
        self::TABLE_PARTITION,
        self::TABLE_AUTO_RANDOM,
    ];

    public const COLUMN_TYPE      = 'type';
    public const COLUMN_DEFAULT   = 'default';
    public const COLUMN_COMMENT   = 'comment';
    public const COLUMN_ATTRIBUTE = 'attribute';

    public const COLUMN_KEYS = [
        self::COLUMN_TYPE,
        self::COLUMN_DEFAULT,
        self::COLUMN_COMMENT,
        self::COLUMN_ATTRIBUTE,
    ];

    public const COLUMN_REQUIRE_KEYS = [
        self::COLUMN_TYPE,
    ];

    public const COLUMN_OPTIONAL_KEYS = [
        self::COLUMN_DEFAULT,
        self::COLUMN_COMMENT,
        self::COLUMN_ATTRIBUTE,
    ];

    public const INDEX_TYPE   = 'is_unique';
    public const INDEX_COLUMN = 'column';

    public const INDEX_REQUIRE_KEYS = [
        self::INDEX_TYPE,
        self::INDEX_COLUMN,
    ];

    public const PARTITION_BY           = 'by';
    public const PARTITION_VALUE        = 'value';
    public const PARTITION_LIST         = 'list';
    public const PARTITION_LESS_THAN    = 'less_than';
    public const PARTITION_IN           = 'in';
    public const PARTITION_ENGINE       = 'engine';
    public const PARTITION_PART_COMMENT = 'comment';
    public const PARTITION_NUM          = 'num';

    public const VIEW_ALGORITHM = 'algorithm';
    public const VIEW_ALIAS     = 'alias';
    public const VIEW_COLUMN    = 'column';
    public const VIEW_FROM      = 'from';

    public const VIEW_KEYS = [
        self::VIEW_COLUMN,
        self::VIEW_FROM,
        self::VIEW_ALIAS,
    ];

    public const VIEW_REQUIRE_KEYS = [
        self::VIEW_COLUMN,
        self::VIEW_FROM,
    ];

    public const VIEW_OPTIONAL_KEYS = [
        self::VIEW_ALIAS,
    ];

    public const JOIN_REFERENCE = 'reference';
    public const JOIN_JOINS     = 'joins';
    public const JOIN_FACTOR    = 'factor';
    public const JOIN_ON        = 'on';

    public const VIEW_RAW_QUERY = 'query';
}
