<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\banking\Bankgiro;
use PhpSpec\ObjectBehavior;

class BankgiroNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BankgiroNode::CLASS);
    }

    function it_is_a_node()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('BankgiroNode');
    }

    function it_can_be_created_using_factory(Bankgiro $bankgiro)
    {
        $bankgiro->getNumber()->willReturn('bankgiro_number');
        $this->beConstructedThrough('fromBankgiro', [$bankgiro]);
        $this->getLineNr()->shouldEqual(0);
        $this->getValue()->shouldEqual('bankgiro_number');
        $this->getAttribute('account')->shouldEqual($bankgiro);
    }
}
