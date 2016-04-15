<?php

namespace byrokrat\autogiro;

class LineTest extends \PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        $string = 'ABCÖÄÅ';

        $line = new Line($string);
        $converted = $line->convertTo('ISO-8859-1');

        $this->assertNotEquals(
            (string)$line,
            (string)$converted
        );

        $this->assertEquals(
            (string)$line,
            (string)$converted->convertTo('UTF-8')
        );
    }

    public function testStartsWith()
    {
        $this->assertFalse(
            (new Line('ABCDE'))->startsWith('BCDE')
        );
        $this->assertTrue(
            (new Line('ABCDE'))->startsWith('AB')
        );
    }

    public function testContains()
    {
        $this->assertFalse(
            (new Line('ABCDE'))->contains('AC')
        );
        $this->assertTrue(
            (new Line('ABCDE'))->contains('CD')
        );
    }

    public function testMatches()
    {
        $this->assertFalse(
            (new Line('ABCDE'))->matches('/F/')
        );
        $this->assertTrue(
            (new Line('ABCDE'))->matches('/D/')
        );
    }

    public function testCapture()
    {
        $this->assertSame(
            [],
            (new Line('ABCDE'))->capture('/F/')
        );
        $this->assertSame(
            ['D'],
            (new Line('ABCDE'))->capture('/D/')
        );
    }

    public function testIsEmpty()
    {
        $this->assertFalse(
            (new Line('sdgf'))->isEmpty()
        );
        $this->assertTrue(
            (new Line(' '))->isEmpty()
        );
        $this->assertTrue(
            (new Line("\r\n\t "))->isEmpty()
        );
    }
}
