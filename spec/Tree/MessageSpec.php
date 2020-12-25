<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Message;
use byrokrat\autogiro\Tree\Container;
use PhpSpec\ObjectBehavior;

class MessageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Message::class);
    }

    function it_is_a_container()
    {
        $this->shouldHaveType(Container::class);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('Message');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Message');
    }
}
