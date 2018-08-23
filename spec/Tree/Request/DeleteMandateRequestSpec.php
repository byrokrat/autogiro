<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Request;

use byrokrat\autogiro\Tree\Request\DeleteMandateRequest;
use byrokrat\autogiro\Tree\Record;
use PhpSpec\ObjectBehavior;

class DeleteMandateRequestSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DeleteMandateRequest::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('DeleteMandateRequest');
    }

    function it_is_a_record()
    {
        $this->shouldHaveType(Record::CLASS);
    }
}
