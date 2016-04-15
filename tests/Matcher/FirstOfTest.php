<?php

namespace byrokrat\autogiro\Matcher;

class FirstOfTest extends \PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $line = $this->prophesize('byrokrat\autogiro\Line')->reveal();

        $matcherA = $this->prophesize('byrokrat\autogiro\Matcher\Matcher');
        $matcherA->match($line)->willThrow(new \byrokrat\autogiro\Exception\InvalidContentException);

        $matcherB = $this->prophesize('byrokrat\autogiro\Matcher\Matcher');
        $matcherB->match($line)->willReturn('foobar');

        $matcher = new FirstOf(
            $matcherA->reveal(),
            $matcherB->reveal()
        );

        $this->assertSame(
            'foobar',
            $matcher->match($line)
        );
    }

    public function testException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidContentException');
        (new FirstOf)->match($this->prophesize('byrokrat\autogiro\Line')->reveal());
    }
}
