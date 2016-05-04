<?php

declare(strict_types=1);

namespace byrokrat\autogiro;

use Prophecy\Argument;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getLineMock(string $str = '')
    {
        $line = $this->prophesize(Line::CLASS);
        $line->__toString()->willReturn($str);
        $line->isEmpty()->willReturn(!$str);
        $line->substr(Argument::type('int'), Argument::type('int'))->willReturn($str);

        return $line->reveal();
    }
}
