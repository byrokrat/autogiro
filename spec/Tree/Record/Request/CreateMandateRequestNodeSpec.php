<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record\Request;

use byrokrat\autogiro\Tree\Record\Request\CreateMandateRequestNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\Account\AccountNode;
use byrokrat\autogiro\Tree\Account\BankgiroNode;
use byrokrat\autogiro\Tree\Id\IdNode;
use PhpSpec\ObjectBehavior;

class CreateMandateRequestNodeSpec extends ObjectBehavior
{
    function let(BankgiroNode $bankgiro, PayerNumberNode $payerNr, AccountNode $account, IdNode $id)
    {
        $this->beConstructedWith(0, $bankgiro, $payerNr, $account, $id);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateMandateRequestNode::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('CreateMandateRequestNode');
    }

    function it_contains_a_line_number($bankgiro, $payerNr, $account, $id)
    {
        $this->beConstructedWith(123, $bankgiro, $payerNr, $account, $id);
        $this->getLineNr()->shouldEqual(123);
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
}
