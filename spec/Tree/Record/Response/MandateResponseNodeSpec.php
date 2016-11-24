<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record\Response;

use byrokrat\autogiro\Tree\Record\Response\MandateResponseNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\Date\DateNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Tree\Account\AccountNode;
use byrokrat\autogiro\Tree\Account\BankgiroNode;
use byrokrat\autogiro\Tree\Id\IdNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MandateResponseNodeSpec extends ObjectBehavior
{
    function let(
        BankgiroNode $bankgiro,
        PayerNumberNode $payerNr,
        AccountNode $account,
        IdNode $id,
        MessageNode $info,
        MessageNode $comment,
        DateNode $date
    ) {
        $this->beConstructedWith(0, $bankgiro, $payerNr, $account, $id, $info, $comment, $date);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MandateResponseNode::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('MandateResponseNode');
    }

    function it_contains_a_line_number($bankgiro, $payerNr, $account, $id, $info, $comment, $date)
    {
        $this->beConstructedWith(100, $bankgiro, $payerNr, $account, $id, $info, $comment, $date);
        $this->getLineNr()->shouldEqual(100);
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getChild('bankgiro')->shouldEqual($bankgiro);
    }

    function it_contains_a_payer_number($payerNr)
    {
        $this->getChild('payer_number')->shouldEqual($payerNr);
    }

    function it_contains_an_account($account)
    {
        $this->getChild('account')->shouldEqual($account);
    }

    function it_contains_an_id($id)
    {
        $this->getChild('id')->shouldEqual($id);
    }

    function it_contains_a_message($info)
    {
        $this->getChild('info')->shouldEqual($info);
    }

    function it_contains_a_comment($comment)
    {
        $this->getChild('comment')->shouldEqual($comment);
    }

    function it_contains_a_date($date)
    {
        $this->getChild('date')->shouldEqual($date);
    }
}
