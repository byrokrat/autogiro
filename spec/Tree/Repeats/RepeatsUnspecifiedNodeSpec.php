<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Repeats;

use byrokrat\autogiro\Tree\Repeats\RepeatsUnspecifiedNode;
use byrokrat\autogiro\Tree\Repeats\RepeatsNode;
use PhpSpec\ObjectBehavior;

class RepeatsUnspecifiedNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RepeatsUnspecifiedNode::CLASS);
    }

    function it_implements_repeats_node_interface()
    {
        $this->shouldHaveType(RepeatsNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('RepeatsUnspecifiedNode');
    }
}
