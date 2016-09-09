<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro;

use byrokrat\autogiro\{ParserFactory, Parser};
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
}
