<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Container;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Visitor\VisitorInterface;
use PhpSpec\ObjectBehavior;

class ContainerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Container::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_default_name()
    {
        $this->getName()->shouldEqual('Container');
    }

    function it_can_set_name()
    {
        $this->beConstructedWith('custom-name');
        $this->getName()->shouldEqual('custom-name');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Container');
    }

    function it_accepts_a_visitor(Node $nodeA, Node $nodeB, VisitorInterface $visitor)
    {
        $this->beConstructedWith('', $nodeA, $nodeB);

        $visitor->visitBefore($this)->shouldBeCalled();
        $nodeA->accept($visitor)->shouldBeCalled();
        $nodeB->accept($visitor)->shouldBeCalled();
        $visitor->visitAfter($this)->shouldBeCalled();

        $this->accept($visitor);
    }

    function it_contains_nodes(Node $nodeA, Node $nodeB)
    {
        $nodeA->getName()->willReturn('nodeA');
        $nodeB->getName()->willReturn('nodeB');

        $this->beConstructedWith('', $nodeA, $nodeB);

        $this->getChildren()->shouldIterateAs([$nodeA, $nodeB]);
        $this->getChild('nodeA')->shouldReturn($nodeA);
        $this->getChild('nodeB')->shouldReturn($nodeB);
    }

    function it_contains_a_line_number(Node $nodeA)
    {
        $this->beConstructedWith('', $nodeA);
        $nodeA->getLineNr()->willReturn(15);
        $this->getLineNr()->shouldEqual(15);
    }

    function it_defaults_to_line_number_zero()
    {
        $this->beConstructedWith();
        $this->getLineNr()->shouldEqual(0);
    }
}
