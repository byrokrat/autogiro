<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\RequestMandateUpdateNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use PhpSpec\ObjectBehavior;

class RequestMandateUpdateNodeSpec extends ObjectBehavior
{
    function let(BankgiroNode $bankgiro, PayerNumberNode $payerNr, BankgiroNode $newBankgiro, PayerNumberNode $newPayerNr)
    {
        $this->beConstructedWith($bankgiro, $payerNr, $newBankgiro, $newPayerNr);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RequestMandateUpdateNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('RequestMandateUpdateNode');
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getChild('bankgiro')->shouldEqual($bankgiro);
    }

    function it_contains_a_payer_nr($payerNr)
    {
        $this->getChild('payer_number')->shouldEqual($payerNr);
    }

    function it_contains_a_new_bankgiro($newBankgiro)
    {
        $this->getChild('new_bankgiro')->shouldEqual($newBankgiro);
    }

    function it_contains_a_new_payer_nr($newPayerNr)
    {
        $this->getChild('new_payer_number')->shouldEqual($newPayerNr);
    }

    function it_contains_a_line_number($bankgiro, $payerNr, $newBankgiro, $newPayerNr)
    {
        $this->beConstructedWith($bankgiro, $payerNr, $newBankgiro, $newPayerNr, 11);
        $this->getLineNr()->shouldEqual(11);
    }
}
