<?php

namespace Conv\Util;

class PartitionType
{
    const SHORT = 0;
    const LONG  = 1;

    const KEY           = 'key';
    const LINEAR_KEY    = 'linear_key';
    const HASH          = 'hash';
    const LINEAR_HASH   = 'linear_hash';
    const LIST          = 'list';
    const LIST_COLUMNS  = 'list_columns';
    const RANGE         = 'range';
    const RANGE_COLUMNS = 'range_columns';

    const METHOD = [
      'KEY'           => self::KEY,
      'LINEAR KEY'    => self::LINEAR_KEY,
      'HASH'          => self::HASH,
      'LINEAR HASH'   => self::LINEAR_HASH,
      'LIST'          => self::LIST,
      'LIST COLUMNS'  => self::LIST_COLUMNS,
      'RANGE'         => self::RANGE,
      'RANGE COLUMNS' => self::RANGE_COLUMNS,
    ];

    const METHOD_TYPE = [
      'KEY'           => self::SHORT,
      'LINEAR KEY'    => self::SHORT,
      'HASH'          => self::SHORT,
      'LINEAR HASH'   => self::SHORT,
      'LIST'          => self::LONG,
      'LIST COLUMNS'  => self::LONG,
      'RANGE'         => self::LONG,
      'RANGE COLUMNS' => self::LONG,
    ];

    const METHOD_OPERATOR = [
      'LIST'          => 'IN',
      'LIST COLUMNS'  => 'IN',
      'RANGE'         => 'LESS THAN',
      'RANGE COLUMNS' => 'LESS THAN',
    ];
}
