<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Interval;
use byrokrat\autogiro\Tree\Message;
use PhpSpec\ObjectBehavior;

class IntervalSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Interval::CLASS);
    }

    function it_implements_the_text_node_interface()
    {
        $this->shouldHaveType(Message::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('Interval');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Message');
    }
}
