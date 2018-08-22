<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Response;

use byrokrat\autogiro\Tree\Response\OutgoingPaymentResponseSection;
use byrokrat\autogiro\Tree\SectionNode;
use PhpSpec\ObjectBehavior;

class OutgoingPaymentResponseSectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OutgoingPaymentResponseSection::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('OutgoingPaymentResponseSection');
    }

    function it_is_a_section()
    {
        $this->shouldHaveType(SectionNode::CLASS);
    }
}
