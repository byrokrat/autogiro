<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Matcher;

use byrokrat\autogiro\Exception\InvalidContentException;

class FirstOfTest extends \byrokrat\autogiro\BaseTestCase
{
    public function testMatch()
    {
        $line = $this->getLineMock();

        $matcherA = $this->prophesize(Matcher::CLASS);
        $matcherA->match($line)->willThrow(new InvalidContentException);

        $matcherB = $this->prophesize(Matcher::CLASS);
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
        $this->setExpectedException(InvalidContentException::CLASS);
        (new FirstOf)->match($this->getLineMock());
    }
}
