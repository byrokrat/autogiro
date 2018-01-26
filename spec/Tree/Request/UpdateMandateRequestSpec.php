<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Request;

use byrokrat\autogiro\Tree\Request\UpdateMandateRequest;
use byrokrat\autogiro\Tree\RecordNode;
use PhpSpec\ObjectBehavior;

class UpdateMandateRequestSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UpdateMandateRequest::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('UpdateMandateRequest');
    }

    function it_is_a_record()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }
}
