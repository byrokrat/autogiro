<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\DateTimeNode;
use byrokrat\autogiro\Tree\DateNode;
use PhpSpec\ObjectBehavior;

class DateTimeNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateTimeNode::CLASS);
    }

    function it_is_a_date_node()
    {
        $this->shouldHaveType(DateNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('DateTimeNode');
    }
}
