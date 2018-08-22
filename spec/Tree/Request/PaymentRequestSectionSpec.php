<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Request;

use byrokrat\autogiro\Tree\Request\PaymentRequestSection;
use byrokrat\autogiro\Tree\SectionNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PaymentRequestSectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PaymentRequestSection::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('PaymentRequestSection');
    }

    function it_is_a_section()
    {
        $this->shouldHaveType(SectionNode::CLASS);
    }
}
