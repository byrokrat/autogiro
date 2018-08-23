<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Request;

use byrokrat\autogiro\Tree\Request\AmendmentRequestSection;
use byrokrat\autogiro\Tree\Section;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AmendmentRequestSectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AmendmentRequestSection::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('AmendmentRequestSection');
    }

    function it_is_a_section()
    {
        $this->shouldHaveType(Section::CLASS);
    }
}
