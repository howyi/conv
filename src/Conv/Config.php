<?php

namespace Conv;

use Howyi\Evi;

class Config
{
    const DEFAULT = [
        'default' => [
            'engine'  => 'InnoDB',
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_bin',
        ],
        'option' => [
            'eval' => false
        ]
    ];

    private function __construct()
    {
    }

    /**
     * @param string $key
     * @param array  $arg
     * @return mixed
     */
    final public static function __callStatic(string $key, array $arg)
    {
        static $config;
        if (is_null($config)) {
            $path = getcwd() . DIRECTORY_SEPARATOR . 'conv.yml';
            if (file_exists($path)) {
                $config = Evi::parse($path, true, '$ref', '$ext');
            } else {
                $config = self::DEFAULT;
            }
        }

        if (array_key_exists($key, $config)) {
            return $config[$key][$arg[0]];
        } else {
            return self::DEFAULT[$key][$arg[0]];
        }
    }
}
