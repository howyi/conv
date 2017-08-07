<?php

namespace Conv;

use Symfony\Component\Yaml\Yaml;

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
                set_error_handler(
                    function ($errno, $errstr, $errfile, $errline) {
                        throw new \ErrorException(
                            $errstr,
                            0,
                            $errno,
                            $errfile,
                            $errline
                        );
                    },
                    E_USER_DEPRECATED
                );
                $config = Yaml::parse(file_get_contents($path));
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
