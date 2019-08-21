<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\AmountVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Obj;
use Money\Money;
use Money\MoneyParser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AmountVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj, MoneyParser $moneyParser)
    {
        $this->beConstructedWith($errorObj, $moneyParser);
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

    function it_creates_valid_amounts($moneyParser, Node $node)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild('Object')->willReturn(false);
        $node->getValueFrom('Text')->willReturn('1230K');

        $money = Money::SEK('-12300');
        $moneyParser->parse('1230K')->willReturn($money);

        $node->addChild(new Obj(1, $money))->shouldBeCalled();

        $this->beforeAmount($node);
    }

    function it_creates_amounts_from_strings_with_broken_charset($moneyParser, Node $node)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild('Object')->willReturn(false);
        $node->getValueFrom('Text')->willReturn('1230¤');

        $money = Money::SEK('-12300');
        $moneyParser->parse('-12300')->willReturn($money);

        $node->addChild(new Obj(1, $money))->shouldBeCalled();

        $this->beforeAmount($node);
    }
}
