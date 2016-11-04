<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro;

use byrokrat\autogiro\Parser;
use byrokrat\autogiro\Grammar;
use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Exception\ParserException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function let(Grammar $grammar, Processor $processor)
    {
        $this->beConstructedWith($grammar, [$processor]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parser::CLASS);
    }

    function it_throws_exception_if_parser_fails($grammar)
    {
        $grammar->parse('')->willThrow(new \Exception);
        $this->shouldThrow(ParserException::CLASS)->duringParse('');
    }

    function it_throws_exception_if_processor_fails($grammar, $processor, Node $node)
    {
        $grammar->parse('')->willReturn($node);
        $processor->resetErrors()->shouldBeCalled();
        $node->accept($processor)->shouldBeCalled();
        $processor->getErrors()->willReturn(['error'])->shouldBeCalled();
        $this->shouldThrow(ParserException::CLASS)->duringParse('');
    }
}
