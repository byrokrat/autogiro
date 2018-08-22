<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\RecordNode;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class RecordNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('RecordNode');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('RecordNode');
    }

    function it_contains_a_line_number()
    {
        $this->beConstructedWith(123, []);
        $this->getLineNr()->shouldEqual(123);
    }

    function it_contains_nodes(Node $node1, Node $node2)
    {
        $this->beConstructedWith(0, ['open' => $node1, 'close' => $node2]);
        $this->getChild('open')->shouldEqual($node1);
        $this->getChild('close')->shouldEqual($node2);
    }
}
