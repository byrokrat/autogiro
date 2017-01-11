<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record\Request;

use byrokrat\autogiro\Tree\Record\Request\OutgoingTransactionRequestNode;
use byrokrat\autogiro\Tree\Record\Request\IncomingTransactionRequestNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use PhpSpec\ObjectBehavior;

class OutgoingTransactionRequestNodeSpec extends ObjectBehavior
{
    function let(
        DateNode $date,
        IntervalNode $ival,
        RepetitionsNode $reps,
        TextNode $space,
        PayerNumberNode $payerNr,
        AmountNode $amount,
        PayeeBankgiroNode $payeeBg,
        TextNode $ref,
        TextNode $endVoid
    ) {
        $this->beConstructedWith(123, $date, $ival, $reps, $space, $payerNr, $amount, $payeeBg, $ref, [$endVoid]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OutgoingTransactionRequestNode::CLASS);
    }

    function it_extends_incoming_transaction()
    {
        $this->shouldHaveType(IncomingTransactionRequestNode::CLASS);
    }
}
