<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\ReferredAccountNode;
use byrokrat\autogiro\Tree\AccountNode;
use PhpSpec\ObjectBehavior;

class ReferredAccountNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ReferredAccountNode::CLASS);
    }

    function it_extends_acount_node()
    {
        $this->shouldHaveType(AccountNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('ReferredAccountNode');
    }

    function it_contains_a_referred_value()
    {
        $this->beConstructedWith(0, 'referred');
        $this->getAttribute('referred_value')->shouldEqual('referred');
    }
}
