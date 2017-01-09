<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\TextProcessor;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\RepeatsNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TextProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TextProcessor::CLASS);
    }

    function a_failing_regexp(TextNode $node)
    {
        $node->getValue()->willReturn('foo');
        $node->getAttribute('validation_regexp')->willReturn('/bar/');
        $node->getLineNr()->willReturn(1);

        return $node;
    }

    function it_captures_invalid_text_nodes(TextNode $textNode)
    {
        $this->beforeTextNode($this->a_failing_regexp($textNode));
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_ignores_text_nodes_without_validation_regexp(TextNode $textNode)
    {
        $textNode->getValue()->willReturn('does-not-match-regexp');
        $textNode->getAttribute('validation_regexp')->willReturn(null);

        $this->beforeTextNode($textNode);
        $this->getErrors()->shouldHaveCount(0);
    }

    function it_ignores_text_nodes_with_valid_content(TextNode $textNode)
    {
        $textNode->getValue()->willReturn('abc');
        $textNode->getAttribute('validation_regexp')->willReturn('/abc/');

        $this->beforeTextNode($textNode);
        $this->getErrors()->shouldHaveCount(0);
    }

    function it_captures_invalid_intervals(IntervalNode $node)
    {
        $this->beforeIntervalNode($this->a_failing_regexp($node));
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_captures_invalid_repititions(RepeatsNode $node)
    {
        $this->beforeRepeatsNode($this->a_failing_regexp($node));
        $this->getErrors()->shouldHaveCount(1);
    }
}
