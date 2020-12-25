<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Parser;

use byrokrat\autogiro\Parser\ParserFactory;
use byrokrat\autogiro\Parser\ParserInterface;
use PhpSpec\ObjectBehavior;

class ParserFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ParserFactory::CLASS);
    }

    function it_creates_parsers()
    {
        $this->createParser()->shouldHaveType(ParserInterface::CLASS);
    }

    function it_creates_parses_with_no_external_visitors()
    {
        $this->createParser(ParserFactory::VISITOR_IGNORE_OBJECTS)->shouldHaveType(ParserInterface::CLASS);
    }
}
