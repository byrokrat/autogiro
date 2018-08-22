<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\id\IdInterface;
use PhpSpec\ObjectBehavior;

class IdNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(IdNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('IdNode');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('IdNode');
    }

    function it_can_be_created_using_factory(IdInterface $id)
    {
        $id->__tostring()->willReturn('id_number');
        $this->beConstructedThrough('fromId', [$id]);
        $this->getLineNr()->shouldEqual(0);
        $this->getValue()->shouldEqual('id_number');
        $this->getAttribute('id')->shouldEqual($id);
    }
}
