<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\DateVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Obj;
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

    function it_does_not_create_if_date_object_exists(Node $dateNode)
    {
        $dateNode->hasChild('Object')->willReturn(true);
        $this->beforeDate($dateNode);
        $dateNode->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_ignores_empty_values(Node $dateNode, Node $number)
    {
        $dateNode->hasChild('Object')->willReturn(false);
        $dateNode->getChild('Number')->willReturn($number);
        $number->getValue()->willReturn('        ');
        $this->beforeDate($dateNode);
        $dateNode->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_date(Node $dateNode, Node $number, $errorObj)
    {
        $dateNode->getLineNr()->willReturn(1);
        $dateNode->hasChild('Object')->willReturn(false);
        $dateNode->getChild('Number')->willReturn($number);
        $number->getValue()->willReturn('this-is-not-a-valid-date');
        $this->beforeDate($dateNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_date(Node $dateNode, Node $number)
    {
        $dateNode->getLineNr()->willReturn(1);
        $dateNode->hasChild('Object')->willReturn(false);
        $dateNode->getChild('Number')->willReturn($number);
        $number->getValue()->willReturn('20161109');

        $dateNode->addChild(Argument::that(function (Obj $obj) {
            return $obj->getValue()->format('Ymd') == '20161109';
        }))->shouldBeCalled();

        $this->beforeDate($dateNode);
    }

    function it_creates_date_times(Node $dateNode, Node $number)
    {
        $dateNode->getLineNr()->willReturn(1);
        $dateNode->hasChild('Object')->willReturn(false);
        $dateNode->getChild('Number')->willReturn($number);
        $number->getValue()->willReturn('20091110193055123456');

        $dateNode->addChild(Argument::that(function (Obj $obj) {
            return $obj->getValue()->format('YmdHis') == '20091110193055';
        }))->shouldBeCalled();

        $this->beforeDate($dateNode);
    }

    function it_creates_short_dates(Node $dateNode, Node $number)
    {
        $dateNode->getLineNr()->willReturn(1);
        $dateNode->hasChild('Object')->willReturn(false);
        $dateNode->getChild('Number')->willReturn($number);
        $number->getValue()->willReturn('820323');

        $dateNode->addChild(Argument::that(function (Obj $obj) {
            return $obj->getValue()->format('ymd') == '820323';
        }))->shouldBeCalled();

        $this->beforeDate($dateNode);
    }
}
