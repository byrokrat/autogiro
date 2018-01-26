<?php

declare(strict_types = 1);

namespace %namespace%;

use %subject%;
use byrokrat\autogiro\Tree\RecordNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class %name% extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(%subject_class%::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('%subject_class%');
    }

    function it_is_a_record()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }
}
