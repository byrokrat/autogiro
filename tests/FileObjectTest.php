<?php

namespace byrokrat\autogiro;

use Mockery as m;

class FileObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testCount()
    {
        $fileObj = new FileObject;
        $this->assertSame(0, count($fileObj));
        $fileObj->addLine(m::mock('byrokrat\autogiro\Line'));
        $this->assertSame(1, count($fileObj));
    }

    public function testIterator()
    {
        $fileObj = new FileObject;
        $this->assertSame(
            [],
            iterator_to_array($fileObj)
        );
        $line = m::mock('byrokrat\autogiro\Line');
        $fileObj->addLine($line);
        $this->assertSame(
            ['0' => $line],
            iterator_to_array($fileObj)
        );
    }

    public function testGetLine()
    {
        $fileObj = new FileObject;
        $line = m::mock('byrokrat\autogiro\Line');
        $fileObj->addLine($line);
        $this->assertSame(
            $line,
            $fileObj->getLine(0)
        );
    }

    public function testLineDoesNotExistException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\RuntimeException');
        $fileObj = new FileObject;
        $fileObj->getLine(0);
    }

    public function testGetFirstLine()
    {
        $fileObj = new FileObject;
        $empty = m::mock('byrokrat\autogiro\Line')->shouldReceive('isEmpty')->andReturn(true)->mock();
        $content = m::mock('byrokrat\autogiro\Line')->shouldReceive('isEmpty')->andReturn(false)->mock();
        $fileObj->addLine($empty);
        $fileObj->addLine($content);
        $this->assertSame(
            $content,
            $fileObj->getFirstLine()
        );
    }

    public function testFirstLineDoesNotExistException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\RuntimeException');
        $fileObj = new FileObject;
        $fileObj->getFirstLine();
    }

    public function testGetLastLine()
    {
        $fileObj = new FileObject;
        $content = m::mock('byrokrat\autogiro\Line')->shouldReceive('isEmpty')->andReturn(false)->mock();
        $empty = m::mock('byrokrat\autogiro\Line')->shouldReceive('isEmpty')->andReturn(true)->mock();
        $fileObj->addLine($content);
        $fileObj->addLine($empty);
        $this->assertSame(
            $content,
            $fileObj->getLastLine()
        );
    }

    public function testLastLineDoesNotExistException()
    {
        $this->setExpectedException('byrokrat\autogiro\Exception\RuntimeException');
        $fileObj = new FileObject;
        $fileObj->getLastLine();
    }

    public function testGetContents()
    {
        $fileObj = new FileObject;
        $fileObj->addLine(
            m::mock('byrokrat\autogiro\Line')->shouldReceive('convertTo')->with('encoding')->andReturn('contents')->mock()
        );
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
