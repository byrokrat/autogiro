<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\ReferredAccount;
use byrokrat\autogiro\Tree\Account;
use PhpSpec\ObjectBehavior;

class ReferredAccountSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ReferredAccount::CLASS);
    }

    function it_extends_acount_node()
    {
        $this->shouldHaveType(Account::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('ReferredAccount');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Account');
    }

    function it_contains_a_referred_value()
    {
        $this->beConstructedWith(0, 'referred');
        $this->getAttribute('referred_value')->shouldEqual('referred');
    }
}
