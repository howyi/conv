<?php

namespace Laminaria\Conv\Util;

class FieldOrderTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $field = 'user_name';
        $previousAfterField = 'user_id';
        $nextAfterField = 'age';
        $fieldOrder = new FieldOrder($field, $previousAfterField, $nextAfterField);
        $this->assertSame($field, $fieldOrder->getField());
        $this->assertSame($previousAfterField, $fieldOrder->getPreviousAfterField());
        $this->assertSame($nextAfterField, $fieldOrder->getNextAfterField());
    }
}
