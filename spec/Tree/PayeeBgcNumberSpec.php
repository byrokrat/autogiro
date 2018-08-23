<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\PayeeBgcNumber;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class PayeeBgcNumberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PayeeBgcNumber::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('PayeeBgcNumber');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('PayeeBgcNumber');
    }
}
