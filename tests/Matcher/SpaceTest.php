<?php

namespace byrokrat\autogiro\Matcher;

class SpaceTest extends MatcherTestCase
{
    public function testMatch()
    {
        $this->assertSame(
            ' ',
            (new Space(4, 1))->match($this->mockLine(3, 1, ' '))
        );
    }

    public function testException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidContentException');
        (new Space(4, 1))->match($this->mockLine(3, 1, '0'));
    }
}
