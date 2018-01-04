<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record\Request;

use byrokrat\autogiro\Tree\Record\Request\RejectDigitalMandateRequestNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class RejectDigitalMandateRequestNodeSpec extends ObjectBehavior
{
    function let(
        PayeeBankgiroNode $bankgiro,
        PayerNumberNode $payerNr,
        TextNode $space,
        TextNode $reject,
        TextNode $endVoid
    ) {
        $this->beConstructedWith(5, $bankgiro, $payerNr, $space, $reject, [$endVoid]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RejectDigitalMandateRequestNode::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('RejectDigitalMandateRequestNode');
    }

    function it_contains_a_line_number()
    {
        $this->getLineNr()->shouldEqual(5);
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getChild('payee_bankgiro')->shouldEqual($bankgiro);
    }

    function it_contains_a_payer_nr($payerNr)
    {
        $this->getChild('payer_number')->shouldEqual($payerNr);
    }

    function it_contains_space($space)
    {
        $this->getChild('space_1')->shouldEqual($space);
    }

    function it_contains_a_reject_symbol($reject)
    {
        $this->getChild('reject')->shouldEqual($reject);
    }

    function it_may_contain_void_ending_nodes($endVoid)
    {
        $this->getChild('end_0')->shouldEqual($endVoid);
    }
}
