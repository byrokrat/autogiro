<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\MessageProcessor;
use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Messages;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MessageProcessor::CLASS);
    }

    function it_fails_on_unvalid_message(MessageNode $messageNode)
    {
        $messageNode->getType()->willReturn('MessageNode');
        $messageNode->getLineNr()->willReturn(1);
        $messageNode->getValue()->willReturn('not-valid');

        $this->visitBefore($messageNode);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_creates_valid_messages(MessageNode $messageNode)
    {
        $messageNode->getType()->willReturn('MessageNode');
        $messageNode->getLineNr()->willReturn(1);
        $messageNode->getValue()->willReturn(key(Messages::MESSAGE_MAP));

        $messageNode->setAttribute('message', Argument::type('string'))->shouldBeCalled();

        $this->visitBefore($messageNode);
        $this->getErrors()->shouldHaveCount(0);
    }
}
