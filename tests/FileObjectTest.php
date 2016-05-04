<?php

declare(strict_types=1);

namespace byrokrat\autogiro;

class FileObjectTest extends BaseTestCase
{
    public function testCount()
    {
        $fileObj = new FileObject;
        $this->assertSame(0, count($fileObj));

        $fileObj->addLine($this->getLineMock());
        $this->assertSame(1, count($fileObj));
    }

    public function testIterator()
    {
        $fileObj = new FileObject;
        $this->assertSame([], iterator_to_array($fileObj));

        $line = $this->getLineMock();
        $fileObj->addLine($line);
        $this->assertSame(['0' => $line], iterator_to_array($fileObj));
    }

    public function testGetLine()
    {
        $fileObj = new FileObject;
        $line = $this->getLineMock();
        $fileObj->addLine($line);
        $this->assertSame($line, $fileObj->getLine(0));
    }

    public function testLineDoesNotExistException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\RuntimeException');
        (new FileObject)->getLine(0);
    }

    public function testGetFirstLine()
    {
        $fileObj = new FileObject;
        $empty = $this->getLineMock('');
        $notEmpty = $this->getLineMock('foobar');
        $fileObj->addLine($empty);
        $fileObj->addLine($notEmpty);
        $this->assertSame($notEmpty, $fileObj->getFirstLine());
    }

    public function testFirstLineDoesNotExistException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\RuntimeException');
        (new FileObject)->getFirstLine();
    }

    public function testGetLastLine()
    {
        $fileObj = new FileObject;
        $empty = $this->getLineMock('');
        $notEmpty = $this->getLineMock('foobar');
        $fileObj->addLine($notEmpty);
        $fileObj->addLine($empty);
        $this->assertSame($notEmpty, $fileObj->getLastLine());
    }

    public function testLastLineDoesNotExistException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\RuntimeException');
        (new FileObject)->getLastLine();
    }

    public function testGetContents()
    {
        $fileObj = new FileObject;

        $line = $this->prophesize(Line::CLASS);
        $line->convertTo('encoding')->willReturn($this->getLineMock('contents'));

        $fileObj->addLine($line->reveal());

        $this->assertSame(
            'contentsnewline',
            $fileObj->getContents('newline', 'encoding')
        );
    }

    public function testCreateFromRawData()
    {
        $fileObj = new FileObject("one\r\ntwo\nthree\r");
        $this->assertSame('one', (string)$fileObj->getLine(0));
        $this->assertSame('two', (string)$fileObj->getLine(1));
        $this->assertSame('three', (string)$fileObj->getLine(2));
        $this->assertSame('', (string)$fileObj->getLine(3));
    }
}
