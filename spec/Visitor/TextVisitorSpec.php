<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\TextVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\TextNode;
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

    function a_failing_regexp(TextNode $node)
    {
        $node->getValue()->willReturn('foo');
        $node->hasAttribute('validation_regexp')->willReturn(true);
        $node->getAttribute('validation_regexp')->willReturn('/bar/');
        $node->getLineNr()->willReturn(1);

        return $node;
    }

    function it_captures_invalid_text_nodes(TextNode $textNode, $errorObj)
    {
        $this->beforeTextNode($this->a_failing_regexp($textNode));
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_ignores_text_nodes_without_validation_regexp(TextNode $textNode, $errorObj)
    {
        $textNode->getValue()->willReturn('does-not-match-regexp');
        $textNode->hasAttribute('validation_regexp')->willReturn(false);

        $this->beforeTextNode($textNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_ignores_text_nodes_with_valid_content(TextNode $textNode, $errorObj)
    {
        $textNode->getValue()->willReturn('abc');
        $textNode->hasAttribute('validation_regexp')->willReturn(true);
        $textNode->getAttribute('validation_regexp')->willReturn('/abc/');

        $this->beforeTextNode($textNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_captures_invalid_repetitions(RepetitionsNode $node, $errorObj)
    {
        $this->beforeRepetitionsNode($this->a_failing_regexp($node));
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_captures_invalid_bgc_customer_numbers(PayeeBgcNumberNode $node, $errorObj)
    {
        $this->beforePayeeBgcNumberNode($this->a_failing_regexp($node));
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_captures_invalid_payer_numbers(PayerNumberNode $node, $errorObj)
    {
        $this->beforePayerNumberNode($this->a_failing_regexp($node));
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
