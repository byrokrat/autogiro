<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\AmountVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Obj;
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

    function it_does_not_create_amount_if_object_exists(Node $amountNode)
    {
        $amountNode->hasChild('Object')->willReturn(true);
        $this->beforeAmount($amountNode);
        $amountNode->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_if_amount_is_empty(Node $amountNode, Node $text)
    {
        $amountNode->hasChild('Object')->willReturn(false);
        $amountNode->getChild('Text')->willReturn($text);
        $text->getValue()->willReturn('    ');
        $this->beforeAmount($amountNode);
        $amountNode->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_amounts(Node $amountNode, Node $text, $errorObj)
    {
        $amountNode->getLineNr()->willReturn(1);
        $amountNode->hasChild('Object')->willReturn(false);
        $amountNode->getChild('Text')->willReturn($text);
        $text->getValue()->willReturn('this-is-not-a-valid-signal-string');
        $this->beforeAmount($amountNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_amounts(Node $amountNode, Node $text)
    {
        $amountNode->getLineNr()->willReturn(1);
        $amountNode->hasChild('Object')->willReturn(false);
        $amountNode->getChild('Text')->willReturn($text);
        $text->getValue()->willReturn('1230K');

        $amountNode->addChild(Argument::that(function (Obj $obj) {
            return (new SEK('-123.02'))->equals($obj->getValue());
        }))->shouldBeCalled();

        $this->beforeAmount($amountNode);
    }
}
