<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Request;

use byrokrat\autogiro\Tree\Request\OutgoingPaymentRequest;
use byrokrat\autogiro\Tree\Record;
use PhpSpec\ObjectBehavior;

class OutgoingPaymentRequestSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OutgoingPaymentRequest::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('OutgoingPaymentRequest');
    }

    function it_is_a_record()
    {
        $this->shouldHaveType(Record::CLASS);
    }
}
