<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\MessageNode;
use PhpSpec\ObjectBehavior;

class IntervalNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IntervalNode::CLASS);
    }

    function it_implements_the_text_node_interface()
    {
        $this->shouldHaveType(MessageNode::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('IntervalNode');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('MessageNode');
    }
}
