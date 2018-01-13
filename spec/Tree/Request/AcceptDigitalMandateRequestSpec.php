<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Request;

use byrokrat\autogiro\Tree\Request\AcceptDigitalMandateRequest;
use byrokrat\autogiro\Tree\RecordNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class AcceptDigitalMandateRequestSpec extends ObjectBehavior
{
    function let(BankgiroNode $bankgiro, PayerNumberNode $payerNr)
    {
        $this->beConstructedWith(0, $bankgiro, $payerNr);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AcceptDigitalMandateRequest::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('AcceptDigitalMandateRequest');
    }

    function it_contains_a_line_number($bankgiro, $payerNr)
    {
        $this->beConstructedWith(3, $bankgiro, $payerNr);
        $this->getLineNr()->shouldEqual(3);
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getChild('payee_bankgiro')->shouldEqual($bankgiro);
    }

    function it_contains_a_payer_nr($payerNr)
    {
        $this->getChild('payer_number')->shouldEqual($payerNr);
    }

    function it_may_contain_void_ending_nodes($bankgiro, $payerNr, TextNode $endVoid)
    {
        $this->beConstructedWith(0, $bankgiro, $payerNr, [$endVoid]);
        $this->getChild('end_0')->shouldEqual($endVoid);
    }
}
