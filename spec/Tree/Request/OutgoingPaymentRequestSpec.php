<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Request;

use byrokrat\autogiro\Tree\Request\OutgoingPaymentRequest;
use byrokrat\autogiro\Tree\Request\IncomingPaymentRequest;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use PhpSpec\ObjectBehavior;

class OutgoingPaymentRequestSpec extends ObjectBehavior
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
        $this->shouldHaveType(OutgoingPaymentRequest::CLASS);
    }

    function it_extends_incoming_payment()
    {
        $this->shouldHaveType(IncomingPaymentRequest::CLASS);
    }
}
