<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\MandateResponseNode;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\MessageNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MandateResponseNodeSpec extends ObjectBehavior
{
    function let(
        BankgiroNode $bankgiro,
        PayerNumberNode $payerNr,
        AccountNode $account,
        IdNode $id,
        MessageNode $info,
        MessageNode $comment,
        \DateTime $date
    ) {
        $this->beConstructedWith($bankgiro, $payerNr, $account, $id, $info, $comment, $date);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MandateResponseNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('MandateResponseNode');
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getChild('bankgiro')->shouldEqual($bankgiro);
    }

    function it_contains_a_payer_number($payerNr)
    {
        $this->getChild('payer_number')->shouldEqual($payerNr);
    }

    function it_contains_an_account($account)
    {
        $this->getChild('account')->shouldEqual($account);
    }

    function it_contains_an_id($id)
    {
        $this->getChild('id')->shouldEqual($id);
    }

    function it_contains_a_message($info)
    {
        $this->getChild('info')->shouldEqual($info);
    }

    function it_contains_a_comment($comment)
    {
        $this->getChild('comment')->shouldEqual($comment);
    }

    function it_contains_a_date($date)
    {
        $this->getAttribute('date')->shouldEqual($date);
    }

    function it_contains_a_line_number($bankgiro, $payerNr, $account, $id, $info, $comment, $date)
    {
        $this->beConstructedWith($bankgiro, $payerNr, $account, $id, $info, $comment, $date, 100);
        $this->getLineNr()->shouldEqual(100);
    }
}
