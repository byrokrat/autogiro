<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\Output;
use byrokrat\autogiro\Writer\OutputInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OutputSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Output::CLASS);
    }

    function it_implements_the_output_interface()
    {
        $this->shouldHaveType(OutputInterface::CLASS);
    }

    function it_captures_output()
    {
        $this->write('foo');
        $this->write('bar');
        $this->getContent()->shouldEqual('foobar');
    }

    function it_can_be_cast_to_string()
    {
        $this->write('baz');
        $this->__tostring()->shouldEqual('baz');
    }
}
