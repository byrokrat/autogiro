<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\MessageVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\Message;
use byrokrat\autogiro\Tree\Interval;
use byrokrat\autogiro\Layouts;
use byrokrat\autogiro\Messages;
use byrokrat\autogiro\Intervals;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MessageVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MessageVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_fails_on_unvalid_message(Message $node, $errorObj)
    {
        $node->hasAttribute('message')->willReturn(false);
        $node->hasAttribute('message_id')->willReturn(false);
        $node->getLineNr()->willReturn(1);
        $node->getValue()->willReturn('not-valid');

        $this->beforeMessage($node);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_messages_from_layout_and_node_value(Message $msgNode, AutogiroFile $fileNode, $errorObj)
    {
        $msgNode->hasAttribute('message')->willReturn(false);
        $msgNode->hasAttribute('message_id')->willReturn(false);
        $msgNode->getValue()->willReturn('0');
        $msgNode->setAttribute('message', Argument::type('string'))->shouldBeCalled();

        $fileNode->getAttribute('layout')->willReturn(Layouts::LAYOUT_PAYMENT_RESPONSE);

        $this->beforeAutogiroFile($fileNode);
        $this->beforeMessage($msgNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_creates_message_from_message_id_if_present(Message $node, $errorObj)
    {
        $node->hasAttribute('message')->willReturn(false);
        $node->getValue()->willReturn('not-valid');
        $node->hasAttribute('message_id')->willReturn(true);
        $node->getAttribute('message_id')->willReturn(key(Messages::MESSAGE_MAP));
        $node->setAttribute('message', Argument::type('string'))->shouldBeCalled();

        $this->beforeMessage($node);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_message_if_attr_is_set(Message $node)
    {
        $node->hasAttribute('message')->willReturn(true);
        $this->beforeMessage($node);
        $node->setAttribute('message', Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_creates_valid_interval_descriptions(Interval $node, $errorObj)
    {
        $node->hasAttribute('message')->willReturn(false);
        $node->hasAttribute('message_id')->willReturn(false);
        $node->getValue()->willReturn(key(Intervals::MESSAGE_MAP));
        $node->setAttribute('message', Argument::type('string'))->shouldBeCalled();

        $this->beforeInterval($node);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
