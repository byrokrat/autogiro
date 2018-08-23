<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Record;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class RecordSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Record::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('Record');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Record');
    }

    function it_contains_a_line_number()
    {
        $this->beConstructedWith(123);
        $this->getLineNr()->shouldEqual(123);
    }

    function it_contains_nodes(Node $node1, Node $node2)
    {
        $node1->getName()->willReturn('node1');
        $node2->getName()->willReturn('node2');
        $this->beConstructedWith(0, $node1, $node2);
        $this->getChild('node1')->shouldEqual($node1);
        $this->getChild('node2')->shouldEqual($node2);
    }
}
