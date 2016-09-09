<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree;
use byrokrat\banking\Bankgiro;
use PhpSpec\ObjectBehavior;

class LayoutNodeSpec extends ObjectBehavior
{
    function let(Tree\OpeningNode $opening, Tree\ClosingNode $closing, Tree\NodeInterface $node)
    {
        $this->beConstructedWith($opening, $closing, $node);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Tree\LayoutNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Tree\NodeInterface::CLASS);
    }

    function it_accepts_a_visitor(Tree\VisitorInterface $visitor, $opening, $closing, $node)
    {
        $visitor->visitLayoutNode($this)->shouldBeCalled();
        $opening->accept($visitor)->shouldBeCalled();
        $closing->accept($visitor)->shouldBeCalled();
        $node->accept($visitor)->shouldBeCalled();

        $this->accept($visitor);
    }

    function it_contains_a_line_number($opening)
    {
        $opening->getLineNr()->willReturn(100);
        $this->getLineNr()->shouldEqual(100);
    }

    function it_contains_a_layout_id($opening)
    {
        $opening->getLayoutId()->willReturn('foobarbaz');
        $this->getLayoutId()->shouldEqual('foobarbaz');
    }

    function it_contains_nodes($opening, $closing, $node)
    {
        $this->getOpeningNode()->shouldEqual($opening);
        $this->getClosingNode()->shouldEqual($closing);
        $this->getContentNodes()->shouldEqual([$node]);
    }
}
