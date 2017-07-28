<?php

namespace Conv;

class TestTest extends \PHPUnit\Framework\TestCase
{
    public function testAssert()
    {
        $this->assertSame('niwatori', 'niwatori');
        $this->assertNotEquals('niwatori', 'niwatoko');
    }
}
