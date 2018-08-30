<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Flag;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class FlagSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Flag::CLASS);
    }

    function it_is_a_node()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_name()
    {
        $this->beConstructedWith('FlagName');
        $this->getName()->shouldEqual('FlagName');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Flag');
    }
}
