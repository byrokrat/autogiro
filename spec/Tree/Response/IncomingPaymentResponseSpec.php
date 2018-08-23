<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Response;

use byrokrat\autogiro\Tree\Response\IncomingPaymentResponse;
use byrokrat\autogiro\Tree\Record;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IncomingPaymentResponseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IncomingPaymentResponse::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('IncomingPaymentResponse');
    }

    function it_is_a_record()
    {
        $this->shouldHaveType(Record::CLASS);
    }
}
