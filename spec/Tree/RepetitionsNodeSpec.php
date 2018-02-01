<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class RepetitionsNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RepetitionsNode::CLASS);
    }

    function it_implements_the_text_node_interface()
    {
        $this->shouldHaveType(TextNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('RepetitionsNode');
    }

    function it_contains_a_regexp()
    {
        $this->getValidationRegexp()->shouldEqual('/^([0-9]{3})|( {3})$/');
    }
}
