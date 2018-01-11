<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record;

use byrokrat\autogiro\Tree\Record\ResponseOpeningRecord;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\BgcNumberNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class ResponseOpeningRecordSpec extends ObjectBehavior
{
    function let(DateNode $date, BgcNumberNode $custNr, BankgiroNode $payeeBg, TextNode $endVoid)
    {
        $this->beConstructedWith(10, $date, $custNr, $payeeBg, [$endVoid]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResponseOpeningRecord::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('ResponseOpeningRecord');
    }

    function it_contains_a_line_number()
    {
        $this->getLineNr()->shouldEqual(10);
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
