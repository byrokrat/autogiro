<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Request;

use byrokrat\autogiro\Tree\Request\IncomingPaymentRequest;
use byrokrat\autogiro\Tree\RecordNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IncomingPaymentRequestSpec extends ObjectBehavior
{
    function let(
        DateNode $date,
        IntervalNode $ival,
        RepetitionsNode $reps,
        TextNode $space,
        PayerNumberNode $payerNr,
        AmountNode $amount,
        BankgiroNode $payeeBg,
        TextNode $ref,
        TextNode $endVoid
    ) {
        $this->beConstructedWith(123, $date, $ival, $reps, $space, $payerNr, $amount, $payeeBg, $ref, [$endVoid]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IncomingPaymentRequest::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_line_number()
    {
        $this->getLineNr()->shouldEqual(123);
    }

    function it_contains_a_date($date)
    {
        $this->getChild('date')->shouldEqual($date);
    }

    function it_contains_an_interval($ival)
    {
        $this->getChild('interval')->shouldEqual($ival);
    }

    function it_contains_repetitions($reps)
    {
        $this->getChild('repetitions')->shouldEqual($reps);
    }

    function it_contains_space($space)
    {
        $this->getChild('space_1')->shouldEqual($space);
    }

    function it_contains_a_payer_nr($payerNr)
    {
        $this->getChild('payer_number')->shouldEqual($payerNr);
    }

    function it_contains_an_amount($amount)
    {
        $this->getChild('amount')->shouldEqual($amount);
    }

    function it_contains_a_bankgiro($payeeBg)
    {
        $this->getChild('payee_bankgiro')->shouldEqual($payeeBg);
    }

    function it_contains_a_reference($ref)
    {
        $this->getChild('reference')->shouldEqual($ref);
    }

    function it_may_contain_void_ending_nodes($endVoid)
    {
        $this->getChild('end_0')->shouldEqual($endVoid);
    }
}
