<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Section;
use byrokrat\autogiro\Tree\Container;
use byrokrat\autogiro\Tree\Record;
use byrokrat\autogiro\Visitor\VisitorInterface;
use PhpSpec\ObjectBehavior;

class SectionSpec extends ObjectBehavior
{
    function let(Record $nodeA, Record $nodeB)
    {
        $nodeA->getName()->willReturn('nodeA');
        $nodeB->getName()->willReturn('nodeB');
        $this->beConstructedWith('', $nodeA, $nodeB);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Section::CLASS);
    }

    function it_is_a_container()
    {
        $this->shouldHaveType(Container::CLASS);
    }

    function it_contains_a_name()
    {
        $this->beConstructedWith('custom-name');
        $this->getName()->shouldEqual('custom-name');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Section');
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
        $this->getChildren()->shouldIterateAs([$nodeA, $nodeB]);
        $this->getChild('nodeA')->shouldReturn($nodeA);
        $this->getChild('nodeB')->shouldReturn($nodeB);
    }

    function it_contains_a_line_number($nodeA)
    {
        $nodeA->getLineNr()->willReturn(15);
        $this->getLineNr()->shouldEqual(15);
    }

    function it_defaults_to_line_number_zero()
    {
        $this->beConstructedWith('');
        $this->getLineNr()->shouldEqual(0);
    }
}
