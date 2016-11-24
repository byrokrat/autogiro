<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record\Request;

use byrokrat\autogiro\Tree\Record\Request\UpdateMandateRequestNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\Account\BankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use PhpSpec\ObjectBehavior;

class UpdateMandateRequestNodeSpec extends ObjectBehavior
{
    function let(BankgiroNode $bankgiro, PayerNumberNode $payerNr, BankgiroNode $newBankgiro, PayerNumberNode $newPayerNr)
    {
        $this->beConstructedWith(0, $bankgiro, $payerNr, $newBankgiro, $newPayerNr);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UpdateMandateRequestNode::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('UpdateMandateRequestNode');
    }

    function it_contains_a_line_number($bankgiro, $payerNr, $newBankgiro, $newPayerNr)
    {
        $this->beConstructedWith(11, $bankgiro, $payerNr, $newBankgiro, $newPayerNr);
        $this->getLineNr()->shouldEqual(11);
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
}
