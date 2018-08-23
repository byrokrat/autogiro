<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Request;

use byrokrat\autogiro\Tree\Request\MandateRequestSection;
use byrokrat\autogiro\Tree\Section;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MandateRequestSectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MandateRequestSection::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('MandateRequestSection');
    }

    function it_is_a_section()
    {
        $this->shouldHaveType(Section::CLASS);
    }
}
