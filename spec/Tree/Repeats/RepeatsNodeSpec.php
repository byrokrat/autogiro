<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Repeats;

use byrokrat\autogiro\Tree\Repeats\RepeatsNode;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class RepeatsNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RepeatsNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('RepeatsNode');
    }
}
