<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\RepeatsNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class RepeatsNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RepeatsNode::CLASS);
    }

    function it_implements_the_text_node_interface()
    {
        $this->shouldHaveType(TextNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('RepeatsNode');
    }

    function it_contains_a_regexp()
    {
        $this->getAttribute('validation_regexp')->shouldEqual('/^([0-9]{3})|( {3})$/');
    }
}
