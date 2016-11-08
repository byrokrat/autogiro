<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\ClosingNode;
use byrokrat\autogiro\Visitor;
use PhpSpec\ObjectBehavior;

class LayoutNodeSpec extends ObjectBehavior
{
    function let(OpeningNode $opening, ClosingNode $closing, Node $node)
    {
        $this->beConstructedWith($opening, $closing, $node);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LayoutNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('LayoutNode');
    }

    function it_contains_nodes($opening, $closing, $node)
    {
        $this->getChild('opening')->shouldEqual($opening);
        $this->getChild('closing')->shouldEqual($closing);
        $this->getChild('content_1')->shouldEqual($node);
    }

    function it_accepts_a_visitor(Visitor $visitor, $opening, $closing, $node)
    {
        $visitor->visitBefore($this)->shouldBeCalled();
        $opening->accept($visitor)->shouldBeCalled();
        $closing->accept($visitor)->shouldBeCalled();
        $node->accept($visitor)->shouldBeCalled();
        $visitor->visitAfter($this)->shouldBeCalled();

        $this->accept($visitor);
    }

    function it_contains_a_line_number($opening)
    {
        $opening->getLineNr()->willReturn(100);
        $this->getLineNr()->shouldEqual(100);
    }
}
