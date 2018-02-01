<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\SectionNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Visitor\VisitorInterface;
use byrokrat\autogiro\Exception\LogicException;
use PhpSpec\ObjectBehavior;

class SectionNodeSpec extends ObjectBehavior
{
    function let(Node $nodeA, Node $nodeB)
    {
        $this->beConstructedWith($nodeA, $nodeB);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SectionNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('SectionNode');
    }

    function it_accepts_a_visitor($nodeA, $nodeB, VisitorInterface $visitor)
    {
        $visitor->visitBefore($this)->shouldBeCalled();
        $nodeA->accept($visitor)->shouldBeCalled();
        $nodeB->accept($visitor)->shouldBeCalled();
        $visitor->visitAfter($this)->shouldBeCalled();

        $this->accept($visitor);
    }

    function it_contains_nodes($nodeA, $nodeB)
    {
        $this->getChildren()->shouldEqual(['1' => $nodeA, '2' => $nodeB]);
        $this->getChild('1')->shouldEqual($nodeA);
        $this->getChild('2')->shouldEqual($nodeB);
    }

    function it_throws_exception_if_index_is_out_of_range()
    {
        $this->shouldThrow(LogicException::CLASS)->duringGetChild('100');
    }

    function it_contains_a_line_number($nodeA)
    {
        $nodeA->getLineNr()->willReturn(15);
        $this->getLineNr()->shouldEqual(15);
    }
}
