<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\TextVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\PayeeBgcNumber;
use byrokrat\autogiro\Tree\PayerNumber;
use byrokrat\autogiro\Tree\Repetitions;
use byrokrat\autogiro\Tree\Text;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TextVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TextVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function a_failing_regexp(Text $node)
    {
        $node->getValue()->willReturn('foo');
        $node->getValidationRegexp()->willReturn('/bar/');
        $node->getLineNr()->willReturn(1);

        return $node;
    }

    function it_captures_invalid_text_nodes(Text $textNode, $errorObj)
    {
        $this->beforeText($this->a_failing_regexp($textNode));
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_ignores_text_nodes_without_regexp(Text $textNode, $errorObj)
    {
        $textNode->getValue()->willReturn('does-not-match-regexp');
        $textNode->getValidationRegexp()->willReturn('');

        $this->beforeText($textNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_ignores_text_nodes_with_valid_content(Text $textNode, $errorObj)
    {
        $textNode->getValue()->willReturn('abc');
        $textNode->getValidationRegexp()->willReturn('/abc/');

        $this->beforeText($textNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_captures_invalid_repetitions(Repetitions $node, $errorObj)
    {
        $this->beforeRepetitions($this->a_failing_regexp($node));
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_captures_invalid_bgc_customer_numbers(PayeeBgcNumber $node, $errorObj)
    {
        $this->beforePayeeBgcNumber($this->a_failing_regexp($node));
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_captures_invalid_payer_numbers(PayerNumber $node, $errorObj)
    {
        $this->beforePayerNumber($this->a_failing_regexp($node));
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
