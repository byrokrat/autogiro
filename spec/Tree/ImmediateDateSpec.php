<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\ImmediateDate;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class ImmediateDateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ImmediateDate::class);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::class);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('ImmediateDate');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('ImmediateDate');
    }
}
