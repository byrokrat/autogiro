<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\AccountNode;
use PhpSpec\ObjectBehavior;

class PayeeBankgiroNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PayeeBankgiroNode::CLASS);
    }

    function it_implements_account_node_interface()
    {
        $this->shouldHaveType(AccountNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('PayeeBankgiroNode');
    }
}
