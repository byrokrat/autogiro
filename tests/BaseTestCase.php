<?php

declare(strict_types = 1);

namespace byrokrat\autogiro;

use Prophecy\Argument;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getLineMock(string $str = ''): Line
    {
        $line = $this->prophesize(Line::CLASS);
        $line->__toString()->willReturn($str);
        $line->isEmpty()->willReturn(!$str);
        $line->substr(Argument::type('int'), Argument::type('int'))->willReturn($str);

        return $line->reveal();
    }

    protected function createSplFileObject(string $content): \SplFileObject
    {
        $filename = sys_get_temp_dir() . '/' . uniqid('byrokrat-autogiro-test');
        file_put_contents($filename, $content);

        return new \SplFileObject($filename);
    }
}
