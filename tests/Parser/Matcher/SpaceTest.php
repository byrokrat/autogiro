<?php

declare(strict_types = 1);

namespace byrokrat\autogiro\Parser\Matcher;

class SpaceTest extends \byrokrat\autogiro\BaseTestCase
{
    public function testMatch()
    {
        $this->assertSame(
            ' ',
            (new Space(4, 1))->match($this->getLineMock(' '))
        );
    }

    public function testException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidContentException');
        (new Space(4, 1))->match($this->getLineMock('0'));
    }
}
