<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Response;

use byrokrat\autogiro\Tree\Response\AmendmentResponse;
use byrokrat\autogiro\Tree\Record;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AmendmentResponseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AmendmentResponse::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('AmendmentResponse');
    }

    function it_is_a_record()
    {
        $this->shouldHaveType(Record::CLASS);
    }
}
