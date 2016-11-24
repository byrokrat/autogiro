<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Account\BankgiroNode;
use byrokrat\autogiro\Tree\BgcCustomerNumberNode;
use byrokrat\autogiro\Tree\Date\DateNode;
use PhpSpec\ObjectBehavior;

class OpeningNodeSpec extends ObjectBehavior
{
    function let(DateNode $date, BgcCustomerNumberNode $custNr, BankgiroNode $bankgiro)
    {
        $this->beConstructedWith(0, '', $date, $custNr, $bankgiro);
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

    function it_contains_a_line_number($date, $custNr, $bankgiro)
    {
        $this->beConstructedWith(10, '', $date, $custNr, $bankgiro);
        $this->getLineNr()->shouldEqual(10);
    }

    function it_contains_a_layout_name($date, $custNr, $bankgiro)
    {
        $this->beConstructedWith(0, 'layout', $date, $custNr, $bankgiro);
        $this->getAttribute('layout_name')->shouldEqual('layout');
    }

    function it_contains_a_date($date)
    {
        $this->getChild('date')->shouldEqual($date);
    }

    function it_contains_a_customer_number($custNr)
    {
        $this->getChild('customer_number')->shouldEqual($custNr);
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getChild('bankgiro')->shouldEqual($bankgiro);
    }
}
