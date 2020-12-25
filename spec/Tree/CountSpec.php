<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Count;
use byrokrat\autogiro\Tree\Container;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Text;
use byrokrat\autogiro\Visitor\VisitorInterface;
use PhpSpec\ObjectBehavior;

class CountSpec extends ObjectBehavior
{
    function let(Node $count)
    {
        $count->getLineNr()->willReturn(1);
        $count->getName()->willReturn(Node::NUMBER);
        $this->beConstructedWith('', $count);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Count::CLASS);
    }

    function it_is_a_container()
    {
        $this->shouldHaveType(Container::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('Count');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Count');
    }

    function it_contains_a_number_node($count)
    {
        $this->getChild(Node::NUMBER)->shouldReturn($count);
    }

    function it_contains_a_text_node($count)
    {
        $this->beConstructedWith('ItemName', $count);
        $this->getChild('Text')->shouldBeLike(new Text(1, 'ItemName'));
    }
}
