<?php

namespace Conv\Util;

class PartitionType
{
    const SHORT = 0;
    const LONG  = 1;

    const KEY           = 'key';
    const HASH          = 'hash';
    const LIST          = 'list';
    const LIST_COLUMNS  = 'list_columns';
    const RANGE         = 'range';
    const RANGE_COLUMNS = 'range_columns';

    const METHOD = [
      'KEY'           => '',
      'HASH'          => '',
      'LIST'          => '',
      'LIST COLUMNS'  => '',
      'RANGE'         => '',
      'RANGE COLUMNS' => '',
    ];

    const METHOD_TYPE = [
      'KEY'           => self::SHORT,
      'HASH'          => self::SHORT,
      'LIST'          => self::LONG,
      'LIST COLUMNS'  => self::LONG,
      'RANGE'         => self::LONG,
      'RANGE COLUMNS' => self::LONG,
    ];
}
