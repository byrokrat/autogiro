<?php

namespace byrokrat\autogiro\Matcher;

class TextTest extends MatcherTestCase
{
    public function testMatch()
    {
        $this->assertSame(
            'TEST',
            (new Text(1, 'TEST'))->match($this->mockLine(0, 4, 'TEST'))
        );
    }

    public function testException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidContentException');
        (new Text(1, 'TEST'))->match($this->mockLine(0, 4, 'test'));
    }
}
