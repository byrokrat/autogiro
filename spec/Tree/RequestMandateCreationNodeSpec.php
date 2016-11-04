<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\RequestMandateCreationNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use PhpSpec\ObjectBehavior;

class RequestMandateCreationNodeSpec extends ObjectBehavior
{
    function let(BankgiroNode $bankgiro, PayerNumberNode $payerNr, AccountNode $account, IdNode $id)
    {
        $this->beConstructedWith($bankgiro, $payerNr, $account, $id);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RequestMandateCreationNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('RequestMandateCreationNode');
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getChild('bankgiro')->shouldEqual($bankgiro);
    }

    function it_contains_a_payer_nr($payerNr)
    {
        $this->getChild('payer_number')->shouldEqual($payerNr);
    }

    function it_contains_an_accont($account)
    {
        $this->getChild('account')->shouldEqual($account);
    }

    function it_containt_an_id($id)
    {
        $this->getChild('id')->shouldEqual($id);
    }

    function it_contains_a_line_number($bankgiro, $payerNr, $account, $id)
    {
        $this->beConstructedWith($bankgiro, $payerNr, $account, $id, 123);
        $this->getLineNr()->shouldEqual(123);
    }
}
