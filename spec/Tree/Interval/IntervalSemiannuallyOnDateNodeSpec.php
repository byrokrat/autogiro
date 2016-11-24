<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Interval;

use byrokrat\autogiro\Tree\Interval\IntervalSemiannuallyOnDateNode;
use byrokrat\autogiro\Tree\Interval\IntervalNode;
use PhpSpec\ObjectBehavior;

class IntervalSemiannuallyOnDateNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IntervalSemiannuallyOnDateNode::CLASS);
    }

    function it_implements_interval_node_interface()
    {
        $this->shouldHaveType(IntervalNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('IntervalSemiannuallyOnDateNode');
    }
}
