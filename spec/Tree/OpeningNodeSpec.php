<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree;
use byrokrat\banking\Bankgiro;
use PhpSpec\ObjectBehavior;

class OpeningNodeSpec extends ObjectBehavior
{
    const LAYOUT_NAME = 'layoutName';
    const CUSTOMER_NR = '123456';
    const LINE_NR = 1;

    function let(\DateTimeImmutable $date, Bankgiro $bankgiro)
    {
        $this->beConstructedWith(self::LAYOUT_NAME, $date, self::CUSTOMER_NR, $bankgiro, self::LINE_NR);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Tree\OpeningNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Tree\NodeInterface::CLASS);
    }

    function it_accepts_a_visitor(Tree\VisitorInterface $visitor)
    {
        $this->accept($visitor);
        $visitor->visitOpeningNode($this)->shouldHaveBeenCalled();
    }

    function it_contains_a_line_number()
    {
        $this->getLineNr()->shouldEqual(self::LINE_NR);
    }

    function it_contains_a_layout_name()
    {
        $this->getLayoutId()->shouldEqual(self::LAYOUT_NAME);
    }

    function it_contains_a_date($date)
    {
        $this->getDate()->shouldEqual($date);
    }

    function it_contains_a_customer_number()
    {
        $this->getCustomerNumber()->shouldEqual(self::CUSTOMER_NR);
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getBankgiro()->shouldEqual($bankgiro);
    }
}
