<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Date;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class DateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Date::class);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::class);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual(Node::DATE);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual(Node::DATE);
    }

    function it_contains_nodes(Node $node)
    {
        $this->beConstructedWith($node);
        $this->getChildren()->shouldIterateAs([$node]);
    }
}
