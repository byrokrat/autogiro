<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\AmountProcessor;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\amount\Currency\SEK;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AmountProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AmountProcessor::CLASS);
    }

    function it_fails_on_unvalid_amounts(AmountNode $amountNode)
    {
        $amountNode->getLineNr()->willReturn(1);
        $amountNode->getType()->willReturn('AmountNode');
        $amountNode->getValue()->willReturn('this-is-not-a-valid-signal-string');
        $this->visitBefore($amountNode);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_creates_valid_amounts(AmountNode $amountNode)
    {
        $amountNode->getType()->willReturn('AmountNode');
        $amountNode->getValue()->willReturn('1230K');

        $amountNode->setAttribute('amount', Argument::exact(new SEK('-123.02')))->shouldBeCalled();

        $this->visitBefore($amountNode);
        $this->getErrors()->shouldHaveCount(0);
    }
}
