<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\DateVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Date\DateNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DateVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_fails_on_unvalid_date(DateNode $dateNode, $errorObj)
    {
        $dateNode->getLineNr()->willReturn(1);
        $dateNode->getType()->willReturn('DateNode');
        $dateNode->getValue()->willReturn('this-is-not-a-valid-date');

        $this->visitBefore($dateNode);

        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_date(DateNode $dateNode, $errorObj)
    {
        $dateNode->getType()->willReturn('DateNode');
        $dateNode->getValue()->willReturn('20161109');

        $dateNode->setAttribute('date', new \DateTimeImmutable('20161109'))->shouldBeCalled();

        $this->visitBefore($dateNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
