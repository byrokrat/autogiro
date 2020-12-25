<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Text;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class TextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Text::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_default_name()
    {
        $this->getName()->shouldEqual('Text');
    }

    function it_can_set_name()
    {
        $this->beConstructedWith(0, '', 'foobar');
        $this->getName()->shouldEqual('foobar');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Text');
    }

    function it_contains_a_regexp()
    {
        $this->beConstructedWith(0, '', '', '/regexp/');
        $this->getValidationRegexp()->shouldEqual('/regexp/');
    }
}
