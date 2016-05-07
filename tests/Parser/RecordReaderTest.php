<?php

declare(strict_types=1);

namespace byrokrat\autogiro\Parser;

use byrokrat\autogiro\Record\Record;

class RecordReaderTest extends \byrokrat\autogiro\BaseTestCase
{
    public function testReadRecord()
    {
        $line = $this->getLineMock('foobar');

        $matcher = $this->prophesize(Matcher\Matcher::CLASS);
        $matcher->match($line)->willReturn((string)$line);

        $reader = new RecordReader(
            [
                'foo' => $matcher->reveal(),
                'bar' => $matcher->reveal()
            ],
            function (array $data) {
                $this->assertSame('foobar', $data['foo']);
                $this->assertSame('foobar', $data['bar']);

                return $this->prophesize(Record::CLASS)->reveal();
            }
        );

        $this->assertInstanceOf(
            Record::CLASS,
            $reader->readRecord($line)
        );
    }
}
