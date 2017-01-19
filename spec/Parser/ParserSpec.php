<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Parser;

use byrokrat\autogiro\Parser\Parser;
use byrokrat\autogiro\Parser\Grammar;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Exception\ContentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function let(Grammar $grammar, Visitor $visitor)
    {
        $this->beConstructedWith($grammar, $visitor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Parser::CLASS);
    }

    function it_creates_trees($grammar, $visitor, FileNode $node)
    {
        $grammar->parse('foobar')->willReturn($node);
        $node->accept($visitor)->shouldBeCalled();
        $this->parse('foobar')->shouldEqual($node);
    }

    function it_throws_parser_exception_if_grammar_fails($grammar)
    {
        $grammar->parse('invalid-ag-file')->willThrow('\InvalidArgumentException');
        $this->shouldThrow(ContentException::CLASS)->duringParse('invalid-ag-file');
    }
}
