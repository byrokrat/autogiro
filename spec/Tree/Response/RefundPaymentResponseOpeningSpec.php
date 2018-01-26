<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Response;

use byrokrat\autogiro\Tree\Response\RefundPaymentResponseOpening;
use byrokrat\autogiro\Tree\RecordNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RefundPaymentResponseOpeningSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RefundPaymentResponseOpening::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('RefundPaymentResponseOpening');
    }

    function it_is_a_record()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }
}
