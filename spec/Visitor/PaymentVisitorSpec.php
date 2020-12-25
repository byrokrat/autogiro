<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\PaymentVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\ImmediateDate;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PaymentVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PaymentVisitor::class);
    }

    function it_fails_if_interval_is_used_with_immediate_date_in_incoming_payment(
        Node $request,
        ImmediateDate $date,
        Node $ival,
        $errorObj
    ) {
        $ival->getValueFrom(Node::NUMBER)->willReturn('1');
        $request->getChild('date')->willReturn($date);
        $request->getChild('interval')->willReturn($ival);
        $request->getValueFrom('repetitions')->willReturn('001');
        $request->getLineNr()->willReturn(1);
        $this->beforeIncomingPaymentRequest($request);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_fails_if_interval_is_used_with_immediate_date_in_outgoing_payment(
        Node $request,
        ImmediateDate $date,
        Node $ival,
        $errorObj
    ) {
        $ival->getValueFrom(Node::NUMBER)->willReturn('1');
        $request->getChild('date')->willReturn($date);
        $request->getChild('interval')->willReturn($ival);
        $request->getValueFrom('repetitions')->willReturn('001');
        $request->getLineNr()->willReturn(1);
        $this->beforeOutgoingPaymentRequest($request);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_fails_if_no_interval_but_repetitions_are_used_in_incoming_payment(
        Node $request,
        ImmediateDate $date,
        Node $ival,
        $errorObj
    ) {
        $ival->getValueFrom(Node::NUMBER)->willReturn('0');
        $request->getChild('date')->willReturn($date);
        $request->getChild('interval')->willReturn($ival);
        $request->getValueFrom('repetitions')->willReturn('001');
        $request->getLineNr()->willReturn(1);
        $this->beforeIncomingPaymentRequest($request);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_fails_if_no_interval_but_repetitions_are_used_in_outgoing_payment(
        Node $request,
        ImmediateDate $date,
        Node $ival,
        $errorObj
    ) {
        $ival->getValueFrom(Node::NUMBER)->willReturn('0');
        $request->getChild('date')->willReturn($date);
        $request->getChild('interval')->willReturn($ival);
        $request->getValueFrom('repetitions')->willReturn('001');
        $request->getLineNr()->willReturn(1);
        $this->beforeOutgoingPaymentRequest($request);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
