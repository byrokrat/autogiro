<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser\Matcher;

class AlphanumTest extends \byrokrat\autogiro\BaseTestCase
{
    public function testMatch()
    {
        $this->assertSame(
            'Aa 12 åÅ - / . &',
            (new Alphanum(2, 6))->match($this->getLineMock('Aa 12 åÅ - / . &'))
        );
    }

    public function testException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidContentException');
        (new Alphanum(2, 6))->match($this->getLineMock('12345~'));
    }
}