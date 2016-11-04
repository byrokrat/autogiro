<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\ClosingNode;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class ClosingNodeSpec extends ObjectBehavior
{
    function let(\DateTimeImmutable $date)
    {
        $this->beConstructedWith($date);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClosingNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('ClosingNode');
    }

    function it_contains_a_date($date)
    {
        $this->getAttribute('date')->shouldEqual($date);
    }

    function it_contains_record_count($date)
    {
        $this->beConstructedWith($date, 5);
        $this->getAttribute('nr_of_posts')->shouldEqual(5);
    }

    function it_contains_a_line_number($date)
    {
        $this->beConstructedWith($date, 0, 10);
        $this->getLineNr()->shouldEqual(10);
    }
}
