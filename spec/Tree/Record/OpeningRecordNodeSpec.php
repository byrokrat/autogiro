<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record;

use byrokrat\autogiro\Tree\Record\OpeningRecordNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class OpeningRecordNodeSpec extends ObjectBehavior
{

    function let(
        TextNode $agTxt,
        TextNode $space1,
        DateNode $date,
        TextNode $space2,
        TextNode $layoutTxt,
        PayeeBgcNumberNode $custNr,
        PayeeBankgiroNode $payeeBg,
        TextNode $endVoid
    ) {
        $this->beConstructedWith(10, $agTxt, $space1, $date, $space2, $layoutTxt, $custNr, $payeeBg, [$endVoid]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OpeningRecordNode::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('OpeningRecordNode');
    }

    function it_contains_a_line_number()
    {
        $this->getLineNr()->shouldEqual(10);
    }

    function it_contains_a_autogiro_text_node($agTxt)
    {
        $this->getChild('autogiro_txt')->shouldEqual($agTxt);
    }

    function it_contains_a_layout_name($layoutTxt)
    {
        $this->getChild('layout_name')->shouldEqual($layoutTxt);
    }

    function it_contains_a_date($date)
    {
        $this->getChild('date')->shouldEqual($date);
    }

    function it_contains_a_customer_number($custNr)
    {
        $this->getChild('payee_bgc_number')->shouldEqual($custNr);
    }

    function it_contains_a_bankgiro($payeeBg)
    {
        $this->getChild('payee_bankgiro')->shouldEqual($payeeBg);
    }

    function it_may_contain_void_ending_nodes($endVoid)
    {
        $this->getChild('end_0')->shouldEqual($endVoid);
    }
}
