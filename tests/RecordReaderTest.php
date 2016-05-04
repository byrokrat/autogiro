<?php

declare(strict_types=1);

namespace byrokrat\autogiro;

class RecordReaderTest extends BaseTestCase
{
    public function testReadRecord()
    {
        $reader = new RecordReader(function (array $data) {
            $this->assertSame('foobar', $data['foo']);
            $this->assertSame('foobar', $data['bar']);

            return $this->prophesize(Record::CLASS)->reveal();
        });

        $line = $this->getLineMock('foobar');

        $matcher = $this->prophesize(Matcher\Matcher::CLASS);
        $matcher->match($line)->willReturn((string)$line);

        $reader->match('foo', $matcher->reveal());
        $reader->match('bar', $matcher->reveal());

        $this->assertInstanceOf(
            Record::CLASS,
            $reader->readRecord($line)
        );
    }
}
