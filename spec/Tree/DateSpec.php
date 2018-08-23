<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Date;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class DateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Date::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('Date');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Date');
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
