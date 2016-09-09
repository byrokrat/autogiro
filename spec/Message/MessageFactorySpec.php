<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Message;

use byrokrat\autogiro\Message\{Message, MessageFactory, Messages};
use byrokrat\autogiro\Exception\RuntimeException;
use PhpSpec\ObjectBehavior;

class MessageFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MessageFactory::CLASS);
    }

    function it_should_create_messages()
    {
        $this->createMessage(key(Messages::MESSAGE_MAP))->shouldHaveType(Message::CLASS);
    }

    function it_should_throw_exception_on_unknown_message_id()
    {
        $this->shouldThrow(RuntimeException::CLASS)->duringCreateMessage('not-a-valid-id');
    }
}
