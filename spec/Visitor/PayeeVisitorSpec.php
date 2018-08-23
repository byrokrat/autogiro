<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\PayeeVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\PayeeBankgiro;
use byrokrat\autogiro\Tree\PayeeBgcNumber;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PayeeVisitorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PayeeVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function let(
        ErrorObject $errorObj,
        PayeeBankgiro $bgA,
        PayeeBankgiro $bgB,
        PayeeBgcNumber $custA,
        PayeeBgcNumber $custB
    ) {
        $this->beConstructedWith($errorObj);
        $bgA->getValue()->willReturn('A');
        $bgB->getValue()->willReturn('B');
        $bgB->getLineNr()->willReturn(1);
        $custA->getValue()->willReturn('A');
        $custB->getValue()->willReturn('B');
        $custB->getLineNr()->willReturn(1);
    }

    function it_ignores_consistent_payee_info($bgA, $custA, $errorObj)
    {
        $this->beforePayeeBankgiro($bgA);
        $this->beforePayeeBankgiro($bgA);
        $this->beforePayeeBgcNumber($custA);
        $this->beforePayeeBgcNumber($custA);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_failes_on_inconsistent_payee_bankgiro($bgA, $bgB, $errorObj)
    {
        $this->beforePayeeBankgiro($bgA);
        $this->beforePayeeBankgiro($bgB);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_failes_on_inconsistent_payee_bgc_numbers($custA, $custB, $errorObj)
    {
        $this->beforePayeeBgcNumber($custA);
        $this->beforePayeeBgcNumber($custB);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_ignores_inconsistent_payee_info_in_different_files($bgA, $bgB, $errorObj)
    {
        $this->beforePayeeBankgiro($bgA);
        $this->beforeAutogiroFile();
        $this->beforePayeeBankgiro($bgB);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
