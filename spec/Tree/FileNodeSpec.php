<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Exception\LogicException;
use PhpSpec\ObjectBehavior;

class FileNodeSpec extends ObjectBehavior
{
    function let(LayoutNode $layoutA, LayoutNode $layoutB)
    {
        $this->beConstructedWith($layoutA, $layoutB);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FileNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_accepts_a_visitor($layoutA, $layoutB, Visitor $visitor)
    {
        $visitor->visitBefore($this)->shouldBeCalled();
        $layoutA->accept($visitor)->shouldBeCalled();
        $layoutB->accept($visitor)->shouldBeCalled();
        $visitor->visitAfter($this)->shouldBeCalled();

        $this->accept($visitor);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('FileNode');
    }

    function it_contains_layouts($layoutA, $layoutB)
    {
        $this->getChildren()->shouldEqual(['1' => $layoutA, '2' => $layoutB]);
        $this->getChild('1')->shouldEqual($layoutA);
        $this->getChild('2')->shouldEqual($layoutB);
    }

    function it_throws_exception_if_index_is_out_of_range()
    {
        $this->shouldThrow(LogicException::CLASS)->duringGetChild('100');
    }

    function it_contains_a_line_number($layoutA)
    {
        $layoutA->getLineNr()->willReturn(15);
        $this->getLineNr()->shouldEqual(15);
    }
}
