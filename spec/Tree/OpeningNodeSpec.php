<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\BankgiroNode;
use PhpSpec\ObjectBehavior;

class OpeningNodeSpec extends ObjectBehavior
{
    function let(\DateTimeImmutable $date, BankgiroNode $bankgiro)
    {
        $this->beConstructedWith('', $date, '', $bankgiro);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OpeningNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('OpeningNode');
    }

    function it_contains_a_layout_name(\DateTimeImmutable $date, BankgiroNode $bankgiro)
    {
        $this->beConstructedWith('layout', $date, '', $bankgiro);
        $this->getAttribute('layout_name')->shouldEqual('layout');
    }

    function it_contains_a_date(\DateTimeImmutable $date)
    {
        $this->getAttribute('date')->shouldEqual($date);
    }

    function it_contains_a_customer_number(\DateTimeImmutable $date, BankgiroNode $bankgiro)
    {
        $this->beConstructedWith('', $date, '1234', $bankgiro);
        $this->getAttribute('customer_number')->shouldEqual('1234');
    }

    function it_contains_a_bankgiro(BankgiroNode $bankgiro)
    {
        $this->getChild('bankgiro')->shouldEqual($bankgiro);
    }

    function it_contains_a_line_number(\DateTimeImmutable $date, BankgiroNode $bankgiro)
    {
        $this->beConstructedWith('', $date, '', $bankgiro, 10);
        $this->getLineNr()->shouldEqual(10);
    }
}
