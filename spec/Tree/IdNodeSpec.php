<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class IdNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IdNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('IdNode');
    }
}
