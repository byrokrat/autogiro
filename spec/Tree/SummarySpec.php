<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Summary;
use byrokrat\autogiro\Tree\Container;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Text;
use byrokrat\autogiro\Visitor\VisitorInterface;
use PhpSpec\ObjectBehavior;

class SummarySpec extends ObjectBehavior
{
    function let(Node $amount)
    {
        $amount->getLineNr()->willReturn(1);
        $amount->getName()->willReturn('Amount');
        $this->beConstructedWith('', $amount);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Summary::CLASS);
    }

    function it_is_a_container()
    {
        $this->shouldHaveType(Container::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('Summary');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Summary');
    }

    function it_contains_a_number_node($amount)
    {
        $this->getChild('Amount')->shouldReturn($amount);
    }

    function it_contains_a_text_node($amount)
    {
        $this->beConstructedWith('ItemName', $amount);
        $this->getChild('Text')->shouldBeLike(new Text(1, 'ItemName'));
    }
}
