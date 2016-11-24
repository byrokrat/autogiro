<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Date;

use byrokrat\autogiro\Tree\Date\DateNode;
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
}
