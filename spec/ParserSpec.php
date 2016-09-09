<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro;

use byrokrat\autogiro\Parser;
use byrokrat\autogiro\Grammar;
use byrokrat\autogiro\Visitor\ValidatingVisitor;
use byrokrat\autogiro\Tree\NodeInterface;
use byrokrat\autogiro\Exception\ParserException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function let(Grammar $grammar, ValidatingVisitor $validator)
    {
        $grammar->resetLineCount()->willReturn(null);
        $this->beConstructedWith($grammar, $validator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parser::CLASS);
    }

    function it_throws_exception_if_phpeg_fails($grammar)
    {
        $grammar->parse('')->willThrow(new \InvalidArgumentException);
        $this->shouldThrow(ParserException::CLASS)->duringParse('');
    }

    function it_throws_exception_if_grammar_fails($grammar)
    {
        $grammar->parse('')->willThrow(new \Exception);
        $grammar->getCurrentLineCount()->willReturn(1)->shouldBeCalled();
        $this->shouldThrow(ParserException::CLASS)->duringParse('');
    }

    function it_throws_exception_if_validator_fails($grammar, $validator, NodeInterface $node)
    {
        $grammar->parse('')->willReturn($node);
        $validator->reset()->shouldBeCalled();
        $node->accept($validator)->shouldBeCalled();
        $validator->hasErrors()->willReturn(true)->shouldBeCalled();
        $validator->getErrors()->willReturn([])->shouldBeCalled();
        $this->shouldThrow(ParserException::CLASS)->duringParse('');
    }
}
