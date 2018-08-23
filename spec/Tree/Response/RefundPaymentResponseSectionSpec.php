<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Response;

use byrokrat\autogiro\Tree\Response\RefundPaymentResponseSection;
use byrokrat\autogiro\Tree\Section;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RefundPaymentResponseSectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RefundPaymentResponseSection::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('RefundPaymentResponseSection');
    }

    function it_is_a_section()
    {
        $this->shouldHaveType(Section::CLASS);
    }
}
