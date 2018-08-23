<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Account;
use byrokrat\autogiro\Tree\Node;
use byrokrat\banking\AccountNumber;
use PhpSpec\ObjectBehavior;

class AccountSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Account::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('Account');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Account');
    }

    function it_can_be_created_using_factory(AccountNumber $account)
    {
        $account->getNumber()->willReturn('account_number');
        $this->beConstructedThrough('fromAccount', [$account]);
        $this->getLineNr()->shouldEqual(0);
        $this->getValue()->shouldEqual('account_number');
        $this->getAttribute('account')->shouldEqual($account);
    }
}
