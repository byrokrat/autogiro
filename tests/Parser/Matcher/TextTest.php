<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser\Matcher;

class TextTest extends \byrokrat\autogiro\BaseTestCase
{
    public function testMatch()
    {
        $this->assertSame(
            'TEST',
            (new Text(1, 'TEST'))->match($this->getLineMock('TEST'))
        );
    }

    public function testException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\InvalidContentException');
        (new Text(1, 'TEST'))->match($this->getLineMock('test'));
    }
}
