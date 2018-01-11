<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record\Request;

use byrokrat\autogiro\Tree\Record\Request\RequestOpeningRecordNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\BgcNumberNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class RequestOpeningRecordNodeSpec extends ObjectBehavior
{
    function let(
        DateNode $date,
        TextNode $agTxt,
        TextNode $space,
        BgcNumberNode $bgcNr,
        BankgiroNode $bankgiro,
        TextNode $end
    ) {
        $this->beConstructedWith(0, $date, $agTxt, $space, $bgcNr, $bankgiro, [$end]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RequestOpeningRecordNode::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('RequestOpeningRecordNode');
    }

    function it_contains_a_line_number($date, $agTxt, $space, $bgcNr, $bankgiro)
    {
        $this->beConstructedWith(10, $date, $agTxt, $space, $bgcNr, $bankgiro);
        $this->getLineNr()->shouldEqual(10);
    }

    function it_contains_a_date($date)
    {
        $this->getChild('date')->shouldEqual($date);
    }

    function it_contains_autogiro_txt_node($agTxt)
    {
        $this->getChild('autogiro_txt')->shouldEqual($agTxt);
    }

    function it_contains_space($space)
    {
        $this->getChild('space')->shouldEqual($space);
    }

    function it_contains_a_customer_number($bgcNr)
    {
        $this->getChild('payee_bgc_number')->shouldEqual($bgcNr);
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getChild('payee_bankgiro')->shouldEqual($bankgiro);
    }

    function it_may_contain_void_ending_nodes($end)
    {
        $this->getChild('end_0')->shouldEqual($end);
    }
}
