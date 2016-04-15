<?php

namespace byrokrat\autogiro\Matcher;

class NumericTest extends MatcherTestCase
{
    public function testMatch()
    {
        $this->assertSame(
            '23',
            (new Numeric(1, 2))->match($this->mockLine(0, 2, '23'))
        );
    }

    public function testException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidContentException');
        (new Numeric(1, 2))->match($this->mockLine(0, 2, '.3'));
    }
}
