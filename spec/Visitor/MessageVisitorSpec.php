<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\MessageVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\MessageNode;
use byrokrat\autogiro\Tree\IntervalNode;
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

    function it_fails_on_unvalid_message(MessageNode $node, $errorObj)
    {
        $node->hasAttribute('message')->willReturn(false);
        $node->getLineNr()->willReturn(1);
        $node->getValue()->willReturn('not-valid');

        $this->beforeMessageNode($node);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_messages(MessageNode $node, $errorObj)
    {
        $node->hasAttribute('message')->willReturn(false);
        $node->getValue()->willReturn(key(Messages::MESSAGE_MAP));
        $node->setAttribute('message', Argument::type('string'))->shouldBeCalled();

        $this->beforeMessageNode($node);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_message_if_attr_is_set(MessageNode $node)
    {
        $node->hasAttribute('message')->willReturn(true);
        $this->beforeMessageNode($node);
        $node->setAttribute('message', Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_creates_valid_interval_descriptions(IntervalNode $node, $errorObj)
    {
        $node->hasAttribute('message')->willReturn(false);
        $node->getValue()->willReturn(key(Intervals::MESSAGE_MAP));
        $node->setAttribute('message', Argument::type('string'))->shouldBeCalled();

        $this->beforeIntervalNode($node);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
