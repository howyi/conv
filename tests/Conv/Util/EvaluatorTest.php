<?php

namespace Conv\Util;

class EvaluatorTest extends \PHPUnit\Framework\TestCase
{
    const EVALUATE_NUM   = 2;
    const EVALUATE_ARRAY = [
        'A' => 4,
        'B' => 8,
    ];

    public function testConstruct()
    {
        $array = [
            'one' => 1,
            'num' => 'php:Conv\Util\EvaluatorTest::EVALUATE_NUM',
            'array' => 'php:Conv\Util\EvaluatorTest::EVALUATE_ARRAY',
        ];
        $expected = [
            'one' => 1,
            'num' => self::EVALUATE_NUM,
            'array' => self::EVALUATE_ARRAY,
        ];
        $this->assertSame($expected, Evaluator::evaluate($array));
    }
}
