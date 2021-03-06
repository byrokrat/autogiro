<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Obj;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class ObjSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Obj::class);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::class);
    }

    function it_contains_a_default_name()
    {
        $this->getName()->shouldEqual(Node::OBJ);
    }

    function it_can_set_name()
    {
        $this->beConstructedWith(0, null, 'custom-name');
        $this->getName()->shouldEqual('custom-name');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual(Node::OBJ);
    }
}
