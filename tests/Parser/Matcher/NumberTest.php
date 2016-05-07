<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser\Matcher;

class NumberTest extends \byrokrat\autogiro\BaseTestCase
{
    public function testMatch()
    {
        $this->assertSame(
            '23',
            (new Number(1, 2))->match($this->getLineMock('23'))
        );
    }

    public function testException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidContentException');
        (new Number(1, 2))->match($this->getLineMock('.3'));
    }
}
