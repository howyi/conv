<?php

namespace Howyi\Conv\Util;

class PartitionType
{
    public const SHORT = 0;
    public const LONG  = 1;

    public const KEY           = 'key';
    public const LINEAR_KEY    = 'linear_key';
    public const HASH          = 'hash';
    public const LINEAR_HASH   = 'linear_hash';
    public const LIST          = 'list';
    public const LIST_COLUMNS  = 'list_columns';
    public const RANGE         = 'range';
    public const RANGE_COLUMNS = 'range_columns';

    public const METHOD = [
      'KEY'           => self::KEY,
      'LINEAR KEY'    => self::LINEAR_KEY,
      'HASH'          => self::HASH,
      'LINEAR HASH'   => self::LINEAR_HASH,
      'LIST'          => self::LIST,
      'LIST COLUMNS'  => self::LIST_COLUMNS,
      'RANGE'         => self::RANGE,
      'RANGE COLUMNS' => self::RANGE_COLUMNS,
    ];

    public const METHOD_TYPE = [
      'KEY'           => self::SHORT,
      'LINEAR KEY'    => self::SHORT,
      'HASH'          => self::SHORT,
      'LINEAR HASH'   => self::SHORT,
      'LIST'          => self::LONG,
      'LIST COLUMNS'  => self::LONG,
      'RANGE'         => self::LONG,
      'RANGE COLUMNS' => self::LONG,
    ];

    public const METHOD_OPERATOR = [
      'LIST'          => 'IN',
      'LIST COLUMNS'  => 'IN',
      'RANGE'         => 'LESS THAN',
      'RANGE COLUMNS' => 'LESS THAN',
    ];
}
