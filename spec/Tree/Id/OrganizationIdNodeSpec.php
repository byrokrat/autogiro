<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Id;

use byrokrat\autogiro\Tree\Id\OrganizationIdNode;
use byrokrat\autogiro\Tree\Id\IdNode;
use PhpSpec\ObjectBehavior;

class OrganizationIdNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OrganizationIdNode::CLASS);
    }

    function it_implements_id_node_interface()
    {
        $this->shouldHaveType(IdNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('OrganizationIdNode');
    }
}
