<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\TextProcessor;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TextProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TextProcessor::CLASS);
    }

    function it_captures_invalid_text_nodes(TextNode $textNode)
    {
        $textNode->getValue()->willReturn('does-not-match-regexp');
        $textNode->getAttribute('validation_regexp')->willReturn('/^FOOBAR$/');
        $textNode->getLineNr()->willReturn(1);

        $this->beforeTextNode($textNode);

        $this->getErrors()->shouldHaveCount(1);
    }

    function it_ignores_text_nodes_without_validation_regexp(TextNode $textNode)
    {
        $textNode->getValue()->willReturn('does-not-match-regexp');
        $textNode->getAttribute('validation_regexp')->willReturn(null);
        $textNode->getLineNr()->willReturn(1);

        $this->beforeTextNode($textNode);

        $this->getErrors()->shouldHaveCount(0);
    }
}
