<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Response;

use byrokrat\autogiro\Tree\Response\IncomingPaymentResponseSection;
use byrokrat\autogiro\Tree\SectionNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IncomingPaymentResponseSectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IncomingPaymentResponseSection::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('IncomingPaymentResponseSection');
    }

    function it_is_a_section()
    {
        $this->shouldHaveType(SectionNode::CLASS);
    }
}
