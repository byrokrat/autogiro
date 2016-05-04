<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Matcher;

class NumericTest extends \byrokrat\autogiro\BaseTestCase
{
    public function testMatch()
    {
        $this->assertSame(
            '23',
            (new Numeric(1, 2))->match($this->getLineMock('23'))
        );
    }

    public function testException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidContentException');
        (new Numeric(1, 2))->match($this->getLineMock('.3'));
    }
}
