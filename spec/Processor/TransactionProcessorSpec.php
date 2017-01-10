<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\TransactionProcessor;
use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\Record\Request\IncomingTransactionRequestNode;
use byrokrat\autogiro\Tree\Record\Request\OutgoingTransactionRequestNode;
use byrokrat\autogiro\Tree\Date\ImmediateDateNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransactionProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TransactionProcessor::CLASS);
    }

    function it_extends_processor()
    {
        $this->shouldHaveType(Processor::CLASS);
    }

    function it_fails_if_interval_is_used_with_immediate_date_in_incoming_transaction(
        IncomingTransactionRequestNode $request,
        ImmediateDateNode $date,
        IntervalNode $ival,
        RepetitionsNode $reps
    ) {
        $ival->getValue()->willReturn('1');
        $reps->getValue()->willReturn('001');
        $request->getChild('date')->willReturn($date);
        $request->getChild('interval')->willReturn($ival);
        $request->getLineNr()->willReturn(1);
        $this->beforeIncomingTransactionRequestNode($request);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_if_interval_is_used_with_immediate_date_in_outgoing_transaction(
        OutgoingTransactionRequestNode $request,
        ImmediateDateNode $date,
        IntervalNode $ival,
        RepetitionsNode $reps
    ) {
        $ival->getValue()->willReturn('1');
        $reps->getValue()->willReturn('001');
        $request->getChild('date')->willReturn($date);
        $request->getChild('interval')->willReturn($ival);
        $request->getLineNr()->willReturn(1);
        $this->beforeOutgoingTransactionRequestNode($request);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_if_no_interval_but_repetitions_are_used_in_incoming_transaction(
        IncomingTransactionRequestNode $request,
        ImmediateDateNode $date,
        IntervalNode $ival,
        RepetitionsNode $reps
    ) {
        $ival->getValue()->willReturn('0');
        $reps->getValue()->willReturn('001');
        $request->getChild('date')->willReturn($date);
        $request->getChild('interval')->willReturn($ival);
        $request->getChild('repetitions')->willReturn($reps);
        $request->getLineNr()->willReturn(1);
        $this->beforeIncomingTransactionRequestNode($request);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_if_no_interval_but_repetitions_are_used_in_outgoing_transaction(
        OutgoingTransactionRequestNode $request,
        ImmediateDateNode $date,
        IntervalNode $ival,
        RepetitionsNode $reps
    ) {
        $ival->getValue()->willReturn('0');
        $reps->getValue()->willReturn('001');
        $request->getChild('date')->willReturn($date);
        $request->getChild('interval')->willReturn($ival);
        $request->getChild('repetitions')->willReturn($reps);
        $request->getLineNr()->willReturn(1);
        $this->beforeIncomingTransactionRequestNode($request);
        $this->getErrors()->shouldHaveCount(1);
    }
}
