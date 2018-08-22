<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\amount\Currency\SEK;
use PhpSpec\ObjectBehavior;

class AmountNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AmountNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('AmountNode');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('AmountNode');
    }

    function it_can_be_created_using_factory(SEK $amount)
    {
        $amount->getSignalString()->willReturn('signal_string');
        $this->beConstructedThrough('fromAmount', [$amount]);
        $this->getLineNr()->shouldEqual(0);
        $this->getValue()->shouldEqual('signal_string');
        $this->getAttribute('amount')->shouldEqual($amount);
    }
}
