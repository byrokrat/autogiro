<?php

declare(strict_types=1);

namespace byrokrat\autogiro;

class SectionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLayoutIdentifier()
    {
        $this->assertSame(
            'foobar',
            (new Section('foobar'))->getLayoutIdentifier()
        );
    }

    public function testOpeningRecordDefault()
    {
        $this->assertInstanceOf(
            Record\NullRecord::CLASS,
            (new Section(''))->getOpeningRecord()
        );
    }

    public function testClosingRecordDefault()
    {
        $this->assertInstanceOf(
            Record\NullRecord::CLASS,
            (new Section(''))->getClosingRecord()
        );
    }

    public function testSetOpeningRecord()
    {
        $record = $this->getMock(Record\Record::CLASS);
        $section = new Section('');
        $section->setOpeningRecord($record);
        $this->assertSame(
            $record,
            $section->getOpeningRecord()
        );
    }

    public function testSetClosingRecord()
    {
        $record = $this->getMock(Record\Record::CLASS);
        $section = new Section('');
        $section->setClosingRecord($record);
        $this->assertSame(
            $record,
            $section->getClosingRecord()
        );
    }

    public function testAddRecord()
    {
        $record = $this->getMock(Record\Record::CLASS);
        $section = new Section('');
        $section->addRecord($record);
        $section->addRecord($record);
        $this->assertSame(
            [$record, $record],
            $section->getRecords()
        );
    }
}
