<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\AmountVisitor;
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

    function it_does_not_create_amount_if_object_exists(Node $node)
    {
        $node->hasChild('Object')->willReturn(true);
        $this->beforeAmount($node);
        $node->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_if_amount_is_empty(Node $node)
    {
        $node->hasChild('Object')->willReturn(false);
        $node->getValueFrom('Text')->willReturn('    ');
        $this->beforeAmount($node);
        $node->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_amounts(Node $node, $errorObj)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild('Object')->willReturn(false);
        $node->getValueFrom('Text')->willReturn('this-is-not-a-valid-signal-string');
        $this->beforeAmount($node);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_amounts(Node $node)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild('Object')->willReturn(false);
        $node->getValueFrom('Text')->willReturn('1230K');

        $node->addChild(Argument::that(function (Obj $obj) {
            return (new SEK('-123.02'))->equals($obj->getValue());
        }))->shouldBeCalled();

        $this->beforeAmount($node);
    }
}
