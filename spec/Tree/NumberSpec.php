<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Number;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class NumberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Number::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_default_name()
    {
        $this->getName()->shouldEqual(Node::NUMBER);
    }

    function it_can_set_name()
    {
        $this->beConstructedWith(0, '', 'custom-name');
        $this->getName()->shouldEqual('custom-name');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual(Node::NUMBER);
    }
}
