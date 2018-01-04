<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record\Response;

use byrokrat\autogiro\Tree\Record\Response\MandateResponseNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class MandateResponseNodeSpec extends ObjectBehavior
{
    const LINE_NR = 100;

    function let(
        PayeeBankgiroNode $bankgiro,
        PayerNumberNode $payerNr,
        AccountNode $account,
        IdNode $id,
        TextNode $space,
        MessageNode $info,
        MessageNode $status,
        DateNode $date,
        TextNode $endVoid
    ) {
        $this->beConstructedWith(
            self::LINE_NR,
            $bankgiro,
            $payerNr,
            $account,
            $id,
            $space,
            $info,
            $status,
            $date,
            [$endVoid]
        );
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

    function it_contains_a_line_number()
    {
        $this->getLineNr()->shouldEqual(self::LINE_NR);
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getChild('payee_bankgiro')->shouldEqual($bankgiro);
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

    function it_contains_space($space)
    {
        $this->getChild('space_1')->shouldEqual($space);
    }

    function it_contains_a_message($info)
    {
        $this->getChild('info')->shouldEqual($info);
    }

    function it_contains_a_status($status)
    {
        $this->getChild('status')->shouldEqual($status);
    }

    function it_contains_a_date($date)
    {
        $this->getChild('date')->shouldEqual($date);
    }

    function it_may_contain_void_ending_nodes($endVoid)
    {
        $this->getChild('end_0')->shouldEqual($endVoid);
    }
}
