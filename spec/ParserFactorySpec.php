<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro;

use byrokrat\autogiro\ParserFactory;
use byrokrat\autogiro\Parser;
use PhpSpec\ObjectBehavior;

class ParserFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ParserFactory::CLASS);
    }

    function it_creates_parsers()
    {
        $this->createParser()->shouldHaveType(Parser::CLASS);
    }

    function it_creates_parses_with_no_external_processors()
    {
        $this->createParser(ParserFactory::VISITOR_IGNORE_EXTERNAL)->shouldHaveType(Parser::CLASS);
    }
}
