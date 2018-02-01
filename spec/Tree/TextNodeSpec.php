<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class TextNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TextNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('TextNode');
    }

    function it_contains_a_regexp()
    {
        $this->beConstructedWith(0, '', '/regexp/');
        $this->getValidationRegexp()->shouldEqual('/regexp/');
    }
}
