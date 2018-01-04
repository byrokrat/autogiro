<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class DateNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('DateNode');
    }

    function it_can_be_created_using_factory()
    {
        $date = new \DateTime('20180101');
        $this->beConstructedThrough('fromDate', [$date]);
        $this->getLineNr()->shouldEqual(0);
        $this->getValue()->shouldEqual('20180101');
        $this->getAttribute('date')->shouldEqual($date);
    }
}
