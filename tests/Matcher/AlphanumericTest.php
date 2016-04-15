<?php

namespace byrokrat\autogiro\Matcher;

class AlphanumericTest extends MatcherTestCase
{
    public function testMatch()
    {
        $this->assertSame(
            'Aa 12 åÅ',
            (new Alphanumeric(2, 6))->match($this->mockLine(1, 6, 'Aa 12 åÅ'))
        );
    }

    public function testException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidContentException');
        (new Alphanumeric(2, 6))->match($this->mockLine(1, 6, '12345~'));
    }
}
