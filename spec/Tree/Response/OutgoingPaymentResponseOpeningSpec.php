<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Response;

use byrokrat\autogiro\Tree\Response\OutgoingPaymentResponseOpening;
use byrokrat\autogiro\Tree\RecordNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OutgoingPaymentResponseOpeningSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OutgoingPaymentResponseOpening::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('OutgoingPaymentResponseOpening');
    }

    function it_is_a_record()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }
}
