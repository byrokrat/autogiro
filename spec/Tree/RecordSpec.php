<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Record;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class RecordSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Record::class);
    }

    function it_contains_a_name()
    {
        $this->beConstructedWith('custom-name');
        $this->getName()->shouldEqual('custom-name');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Record');
    }

    function it_contains_a_line_number(Node $node)
    {
        $this->beConstructedWith('', $node);
        $node->getLineNr()->willReturn(15);
        $this->getLineNr()->shouldEqual(15);
    }

    function it_contains_nodes(Node $node1, Node $node2)
    {
        $node1->getName()->willReturn('node1');
        $node2->getName()->willReturn('node2');
        $this->beConstructedWith('', $node1, $node2);
        $this->getChild('node1')->shouldEqual($node1);
        $this->getChild('node2')->shouldEqual($node2);
    }

    function it_ignores_null_nodes(Node $node)
    {
        $this->beConstructedWith('', null, $node);
        $this->getChildren()->shouldIterateAs([$node]);
    }
}
