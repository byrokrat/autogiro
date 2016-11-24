<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record\Request;

use byrokrat\autogiro\Tree\Record\Request\AcceptMandateRequestNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\Account\BankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use PhpSpec\ObjectBehavior;

class AcceptMandateRequestNodeSpec extends ObjectBehavior
{
    function let(BankgiroNode $bankgiro, PayerNumberNode $payerNr)
    {
        $this->beConstructedWith(0, $bankgiro, $payerNr);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AcceptMandateRequestNode::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('AcceptMandateRequestNode');
    }

    function it_contains_a_line_number($bankgiro, $payerNr)
    {
        $this->beConstructedWith(3, $bankgiro, $payerNr);
        $this->getLineNr()->shouldEqual(3);
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getChild('bankgiro')->shouldEqual($bankgiro);
    }

    function it_contains_a_payer_nr($payerNr)
    {
        $this->getChild('payer_number')->shouldEqual($payerNr);
    }
}
