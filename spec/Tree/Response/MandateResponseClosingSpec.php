<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record;

use byrokrat\autogiro\Tree\Response\MandateResponseClosing;
use byrokrat\autogiro\Tree\Record;
use PhpSpec\ObjectBehavior;

class MandateResponseClosingSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MandateResponseClosing::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('MandateResponseClosing');
    }

    function it_is_a_record()
    {
        $this->shouldHaveType(Record::CLASS);
    }
}
