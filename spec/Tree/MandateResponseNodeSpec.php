<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree;
use byrokrat\autogiro\Message\Message;
use byrokrat\banking\{AccountNumber, Bankgiro};
use byrokrat\id\Id;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MandateResponseNodeSpec extends ObjectBehavior
{
    const PAYER_NR = '345678';
    const LINE_NR = 1;

    function let(Bankgiro $bankgiro, AccountNumber $account, Id $id, Message $info, Message $comment, \DateTime $date)
    {
        $this->beConstructedWith($bankgiro, self::PAYER_NR, $account, $id, $info, $comment, $date, self::LINE_NR);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Tree\MandateResponseNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Tree\NodeInterface::CLASS);
    }

    function it_accepts_a_visitor(Tree\VisitorInterface $visitor)
    {
        $this->accept($visitor);
        $visitor->visitMandateResponseNode($this)->shouldHaveBeenCalled();
    }

    function it_contains_a_line_number()
    {
        $this->getLineNr()->shouldEqual(self::LINE_NR);
    }

    function it_contains_a_bankgiro($bankgiro)
    {
        $this->getBankgiro()->shouldEqual($bankgiro);
    }

    function it_contains_a_payer_number()
    {
        $this->getPayerNumber()->shouldEqual(self::PAYER_NR);
    }

    function it_contains_an_account($account)
    {
        $this->getAccount()->shouldEqual($account);
    }

    function it_contains_an_id($id)
    {
        $this->getId()->shouldEqual($id);
    }

    function it_contains_messages($info, $comment)
    {
        $info->getCode()->willReturn('foo')->shouldBeCalled();
        $comment->getCode()->willReturn('bar')->shouldBeCalled();

        $this->getMessages()->shouldEqual([$info, $comment]);

        $this->hasMessage('foo')->shouldEqual(true);
        $this->hasMessage('bar')->shouldEqual(true);
        $this->hasMessage('baz')->shouldEqual(false);
    }

    function it_contains_a_date($date)
    {
        $this->getDate()->shouldEqual($date);
    }
}
