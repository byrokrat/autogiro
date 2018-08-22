<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class PayerNumberNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PayerNumberNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('PayerNumberNode');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('PayerNumberNode');
    }
}
