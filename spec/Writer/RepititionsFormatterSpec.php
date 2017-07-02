<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\RepititionsFormatter;
use byrokrat\autogiro\Exception\LogicException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RepititionsFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RepititionsFormatter::CLASS);
    }

    function it_formats_regular_number()
    {
        $this->format(123)->shouldEqual('123');
    }

    function it_formats_cero()
    {
        $this->format(0)->shouldEqual('   ');
    }

    function it_pads_low_numbers()
    {
        $this->format(1)->shouldEqual('001');
    }

    function it_throws_exception_on_high_number()
    {
        $this->shouldThrow(LogicException::CLASS)->duringFormat(1000);
    }

    function it_throws_exception_on_low_number()
    {
        $this->shouldThrow(LogicException::CLASS)->duringFormat(-1);
    }
}
