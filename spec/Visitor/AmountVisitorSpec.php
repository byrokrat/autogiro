<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\AmountVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\amount\Currency\SEK;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AmountVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AmountVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_fails_on_unvalid_amounts(AmountNode $amountNode, $errorObj)
    {
        $amountNode->getLineNr()->willReturn(1);
        $amountNode->getType()->willReturn('AmountNode');
        $amountNode->getValue()->willReturn('this-is-not-a-valid-signal-string');
        $this->visitBefore($amountNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_amounts(AmountNode $amountNode, $errorObj)
    {
        $amountNode->getType()->willReturn('AmountNode');
        $amountNode->getValue()->willReturn('1230K');

        $amountNode->setAttribute('amount', Argument::exact(new SEK('-123.02')))->shouldBeCalled();

        $this->visitBefore($amountNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
