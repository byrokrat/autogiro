<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\RecordNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Visitor\Visitor;
use PhpSpec\ObjectBehavior;

class LayoutNodeSpec extends ObjectBehavior
{
    function let(RecordNode $record1, RecordNode $record2)
    {
        $record1->hasChild('layout_name')->willReturn(false);
        $this->beConstructedWith('', $record1, $record2);
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

    function it_contains_nodes($record1, $record2)
    {
        $this->getChild('1')->shouldEqual($record1);
        $this->getChild('2')->shouldEqual($record2);
    }

    function it_accepts_a_visitor(Visitor $visitor, $record1, $record2)
    {
        $visitor->visitBefore($this)->shouldBeCalled();
        $record1->accept($visitor)->shouldBeCalled();
        $record2->accept($visitor)->shouldBeCalled();
        $visitor->visitAfter($this)->shouldBeCalled();

        $this->accept($visitor);
    }

    function it_contains_a_line_number($record1)
    {
        $record1->getLineNr()->willReturn(100);
        $this->getLineNr()->shouldEqual(100);
    }

    function it_sets_layout_name_from_argument()
    {
        $this->beConstructedWith('some-name');
        $this->getAttribute('layout_name')->shouldEqual('some-name');
    }
}
