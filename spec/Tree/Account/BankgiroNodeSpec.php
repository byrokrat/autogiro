<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Account;

use byrokrat\autogiro\Tree\Account\BankgiroNode;
use byrokrat\autogiro\Tree\Account\AccountNode;
use PhpSpec\ObjectBehavior;

class BankgiroNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BankgiroNode::CLASS);
    }

    function it_implements_account_node_interface()
    {
        $this->shouldHaveType(AccountNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('BankgiroNode');
    }
}
