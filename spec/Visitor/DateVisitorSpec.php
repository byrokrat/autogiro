<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\DateVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\DateTimeNode;
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
        $dateNode->hasAttribute('date')->willReturn(false);
        $dateNode->getLineNr()->willReturn(1);
        $dateNode->getValue()->willReturn('this-is-not-a-valid-date');
        $this->beforeDateNode($dateNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_date(DateNode $dateNode, $errorObj)
    {
        $dateNode->hasAttribute('date')->willReturn(false);
        $dateNode->getValue()->willReturn('20161109');
        $dateNode->setAttribute('date', new \DateTimeImmutable('20161109'))->shouldBeCalled();
        $this->beforeDateNode($dateNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_date_if_attr_is_set(DateNode $dateNode)
    {
        $dateNode->hasAttribute('date')->willReturn(true);
        $dateNode->getValue()->willReturn('20161109');
        $this->beforeDateNode($dateNode);
        $dateNode->setAttribute('date', Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_creates_date_times(DateTimeNode $dateNode, $errorObj)
    {
        $dateNode->hasAttribute('date')->willReturn(false);
        $dateNode->getValue()->willReturn('20091110193055123456');
        $dateNode->setAttribute('date', new \DateTimeImmutable('20091110193055'))->shouldBeCalled();
        $this->beforeDateTimeNode($dateNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
