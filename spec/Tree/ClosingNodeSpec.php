<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\ClosingNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Date\DateNode;
use PhpSpec\ObjectBehavior;

class ClosingNodeSpec extends ObjectBehavior
{
    function let(DateNode $date)
    {
        $this->beConstructedWith(0, $date);
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

    function it_contains_a_line_number($date)
    {
        $this->beConstructedWith(10, $date);
        $this->getLineNr()->shouldEqual(10);
    }

    function it_contains_a_date($date)
    {
        $this->getChild('date')->shouldEqual($date);
    }

    function it_contains_record_count($date)
    {
        $this->beConstructedWith(0, $date, 5);
        $this->getAttribute('nr_of_posts')->shouldEqual(5);
    }
}
