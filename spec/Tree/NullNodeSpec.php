<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\NullNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Visitor\VisitorInterface;
use PhpSpec\ObjectBehavior;

class NullNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NullNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('NullNode');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('NullNode');
    }

    function it_contains_a_line_number()
    {
        $this->getLineNr()->shouldReturn(0);
    }

    function it_contains_a_value()
    {
        $this->getValue()->shouldReturn(null);
    }

    function it_is_a_null_node()
    {
        $this->isNull()->shouldReturn(true);
    }
}
