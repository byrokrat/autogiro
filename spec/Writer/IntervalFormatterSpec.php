<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\IntervalFormatter;
use byrokrat\autogiro\Exception\LogicException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IntervalFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IntervalFormatter::CLASS);
    }

    function it_formats_intervals()
    {
        $this->format('1')->shouldEqual('1');
    }

    function it_throws_exception_on_invalid_intervals()
    {
        $this->shouldThrow(LogicException::CLASS)->duringFormat('-1');
        $this->shouldThrow(LogicException::CLASS)->duringFormat('9');
    }
}
