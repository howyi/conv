<?php

namespace Conv\Util;

class Evaluator
{
    const PREFIX = 'php:';

    /**
     * @param array $array
     * @return array
     */
    public static function evaluate(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::evaluate($value);
            }
            if (is_string($value) and substr($value, 0, 4) === self::PREFIX) {
                $evalCode = sprintf(
                    'return (%s);',
                    ltrim($value, self::PREFIX)
                );
                $array[$key] = eval($evalCode);
            }
        }
        return $array;
    }
}
