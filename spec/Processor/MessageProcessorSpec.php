<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\MessageProcessor;
use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Messages;
use byrokrat\autogiro\Intervals;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MessageProcessor::CLASS);
    }

    function it_extends_processor()
    {
        $this->shouldHaveType(Processor::CLASS);
    }

    function it_fails_on_unvalid_message(MessageNode $node)
    {
        $node->getLineNr()->willReturn(1);
        $node->getValue()->willReturn('not-valid');

        $this->beforeMessageNode($node);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_creates_valid_messages(MessageNode $node)
    {
        $node->getValue()->willReturn(key(Messages::MESSAGE_MAP));
        $node->setAttribute('message', Argument::type('string'))->shouldBeCalled();

        $this->beforeMessageNode($node);
        $this->getErrors()->shouldHaveCount(0);
    }

    function it_creates_valid_interval_descriptions(IntervalNode $node)
    {
        $node->getValue()->willReturn(key(Intervals::MESSAGE_MAP));
        $node->setAttribute('message', Argument::type('string'))->shouldBeCalled();

        $this->beforeIntervalNode($node);
        $this->getErrors()->shouldHaveCount(0);
    }
}
