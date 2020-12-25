<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\DateVisitor;
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
        $this->shouldHaveType(DateVisitor::class);
    }

    function it_does_not_create_if_date_object_exists(Node $node)
    {
        $node->hasChild(Node::OBJ)->willReturn(true);
        $this->beforeDate($node);
        $node->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_ignores_empty_values(Node $node)
    {
        $node->hasChild(Node::OBJ)->willReturn(false);
        $node->getValueFrom(Node::NUMBER)->willReturn('        ');
        $this->beforeDate($node);
        $node->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_date(Node $node, $errorObj)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild(Node::OBJ)->willReturn(false);
        $node->getValueFrom(Node::NUMBER)->willReturn('this-is-not-a-valid-date');
        $this->beforeDate($node);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_date(Node $node)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild(Node::OBJ)->willReturn(false);
        $node->getValueFrom(Node::NUMBER)->willReturn('20161109');

        $node->addChild(Argument::that(function (Obj $obj) {
            return $obj->getValue()->format('Ymd') == '20161109';
        }))->shouldBeCalled();

        $this->beforeDate($node);
    }

    function it_creates_date_times(Node $node)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild(Node::OBJ)->willReturn(false);
        $node->getValueFrom(Node::NUMBER)->willReturn('20091110193055123456');

        $node->addChild(Argument::that(function (Obj $obj) {
            return $obj->getValue()->format('YmdHis') == '20091110193055';
        }))->shouldBeCalled();

        $this->beforeDate($node);
    }

    function it_creates_short_dates(Node $node)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild(Node::OBJ)->willReturn(false);
        $node->getValueFrom(Node::NUMBER)->willReturn('820323');

        $node->addChild(Argument::that(function (Obj $obj) {
            return $obj->getValue()->format('ymd') == '820323';
        }))->shouldBeCalled();

        $this->beforeDate($node);
    }
}
