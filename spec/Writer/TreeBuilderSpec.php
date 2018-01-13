<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Writer\IntervalFormatter;
use byrokrat\autogiro\Writer\RepititionsFormatter;
use byrokrat\autogiro\Layouts;
use byrokrat\autogiro\Tree\Request\RequestOpening;
use byrokrat\autogiro\Tree\Request\AcceptDigitalMandateRequest;
use byrokrat\autogiro\Tree\Request\CreateMandateRequest;
use byrokrat\autogiro\Tree\Request\DeleteMandateRequest;
use byrokrat\autogiro\Tree\Request\RejectDigitalMandateRequest;
use byrokrat\autogiro\Tree\Request\UpdateMandateRequest;
use byrokrat\autogiro\Tree\Request\IncomingPaymentRequest;
use byrokrat\autogiro\Tree\Request\OutgoingPaymentRequest;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\ImmediateDateNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\BgcNumberNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\banking\AccountNumber;
use byrokrat\banking\Bankgiro;
use byrokrat\id\IdInterface;
use byrokrat\amount\Currency\SEK;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TreeBuilderSpec extends ObjectBehavior
{
    const BCG_NR = 'bgcNr';
    const BANKGIRO = 'bankgiro';
    const DATE = 'date';

    function let(
        Bankgiro $bankgiro,
        \DateTime $date,
        IntervalFormatter $intervalFormatter,
        RepititionsFormatter $repsFormatter
    ) {
        $bankgiro->getNumber()->willReturn(self::BANKGIRO);
        $date->format('Ymd')->willReturn(self::DATE);
        $this->beConstructedWith(self::BCG_NR, $bankgiro, $date, $intervalFormatter, $repsFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TreeBuilder::CLASS);
    }

    function it_can_reset()
    {
        $this->addDeleteMandateRequest('payerNr');
        $this->reset();
        $this->buildTree()->shouldBeLike(new FileNode);
    }

    function a_tree(string $layoutName, $bankgiro, $date, ...$nodes)
    {
        return new FileNode(
            new LayoutNode(
                $layoutName,
                $this->an_opening_record_node($bankgiro, $date),
                ...$nodes
            )
        );
    }

    function an_opening_record_node($bankgiro, $date)
    {
        return new RequestOpening(
            0,
            DateNode::fromDate($date->getWrappedObject()),
            new TextNode(0, 'AUTOGIRO'),
            new TextNode(0, str_pad('', 44)),
            new BgcNumberNode(0, self::BCG_NR),
            BankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
            [new TextNode(0, '  ')]
        );
    }

    function it_builds_create_mandate_trees($bankgiro, $date, AccountNumber $account, IdInterface $id)
    {
        $account->getNumber()->willReturn('account_number');
        $id->__tostring()->willReturn('id_number');

        $this->addCreateMandateRequest('payerNr', $account, $id);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_MANDATE_REQUEST,
                $bankgiro,
                $date,
                new CreateMandateRequest(
                    0,
                    BankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new PayerNumberNode(0, 'payerNr'),
                    AccountNode::fromAccount($account->getWrappedObject()),
                    IdNode::fromId($id->getWrappedObject()),
                    [new TextNode(0, str_pad('', 24))]
                )
            )
        );
    }

    function it_builds_delete_mandate_trees($bankgiro, $date)
    {
        $this->addDeleteMandateRequest('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_MANDATE_REQUEST,
                $bankgiro,
                $date,
                new DeleteMandateRequest(
                    0,
                    BankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new PayerNumberNode(0, 'payerNr'),
                    [new TextNode(0, str_pad('', 52))]
                )
            )
        );
    }

    function it_builds_accept_mandate_trees($bankgiro, $date)
    {
        $this->addAcceptDigitalMandateRequest('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_MANDATE_REQUEST,
                $bankgiro,
                $date,
                new AcceptDigitalMandateRequest(
                    0,
                    BankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new PayerNumberNode(0, 'payerNr'),
                    [new TextNode(0, str_pad('', 52))]
                )
            )
        );
    }

    function it_builds_reject_mandate_trees($bankgiro, $date)
    {
        $this->addRejectDigitalMandateRequest('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_MANDATE_REQUEST,
                $bankgiro,
                $date,
                new RejectDigitalMandateRequest(
                    0,
                    BankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new PayerNumberNode(0, 'payerNr'),
                    new TextNode(0, str_pad('', 48)),
                    new TextNode(0, 'AV'),
                    [new TextNode(0, '  ')]
                )
            )
        );
    }

    function it_builds_update_mandate_trees($bankgiro, $date)
    {
        $this->addUpdateMandateRequest('foo', 'bar');

        $payeeBgNode = BankgiroNode::fromBankgiro($bankgiro->getWrappedObject());

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_MANDATE_REQUEST,
                $bankgiro,
                $date,
                new UpdateMandateRequest(
                    0,
                    $payeeBgNode,
                    new PayerNumberNode(0, 'foo'),
                    $payeeBgNode,
                    new PayerNumberNode(0, 'bar'),
                    [new TextNode(0, str_pad('', 26))]
                )
            )
        );
    }

    function it_builds_incoming_payment_trees(SEK $amount, $bankgiro, $date, $intervalFormatter, $repsFormatter)
    {
        $intervalFormatter->format(0)->shouldBeCalled()->willReturn('formatted_interval');
        $repsFormatter->format(1)->shouldBeCalled()->willReturn('formatted_repititions');
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addIncomingPaymentRequest('foobar', $amount, $date, 'ref', 0, 1);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_PAYMENT_REQUEST,
                $bankgiro,
                $date,
                new IncomingPaymentRequest(
                    0,
                    DateNode::fromDate($date->getWrappedObject()),
                    new IntervalNode(0, 'formatted_interval'),
                    new RepetitionsNode(0, 'formatted_repititions'),
                    new TextNode(0, ' '),
                    new PayerNumberNode(0, 'foobar'),
                    AmountNode::fromAmount($amount->getWrappedObject()),
                    BankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new TextNode(0, '             ref', '/^.{16}$/'),
                    [new TextNode(0, str_pad('', 11))]
                )
            )
        );
    }

    function it_builds_outgoing_payment_trees(SEK $amount, $bankgiro, $date, $intervalFormatter, $repsFormatter)
    {
        $intervalFormatter->format(0)->shouldBeCalled()->willReturn('formatted_interval');
        $repsFormatter->format(1)->shouldBeCalled()->willReturn('formatted_repititions');
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addOutgoingPaymentRequest('foobar', $amount, $date, 'ref', 0, 1);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_PAYMENT_REQUEST,
                $bankgiro,
                $date,
                new OutgoingPaymentRequest(
                    0,
                    DateNode::fromDate($date->getWrappedObject()),
                    new IntervalNode(0, 'formatted_interval'),
                    new RepetitionsNode(0, 'formatted_repititions'),
                    new TextNode(0, ' '),
                    new PayerNumberNode(0, 'foobar'),
                    AmountNode::fromAmount($amount->getWrappedObject()),
                    BankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new TextNode(0, '             ref', '/^.{16}$/'),
                    [new TextNode(0, str_pad('', 11))]
                )
            )
        );
    }

    function it_builds_immediate_incoming_payment_trees(SEK $amount, $bankgiro, $date)
    {
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addImmediateIncomingPaymentRequest('foobar', $amount, 'ref');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_PAYMENT_REQUEST,
                $bankgiro,
                $date,
                new IncomingPaymentRequest(
                    0,
                    new ImmediateDateNode,
                    new IntervalNode(0, '0'),
                    new RepetitionsNode(0, '   '),
                    new TextNode(0, ' '),
                    new PayerNumberNode(0, 'foobar'),
                    AmountNode::fromAmount($amount->getWrappedObject()),
                    BankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new TextNode(0, '             ref', '/^.{16}$/'),
                    [new TextNode(0, str_pad('', 11))]
                )
            )
        );
    }

    function it_builds_immediate_outgoing_payment_trees(SEK $amount, $bankgiro, $date)
    {
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addImmediateOutgoingPaymentRequest('foobar', $amount, 'ref');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_PAYMENT_REQUEST,
                $bankgiro,
                $date,
                new OutgoingPaymentRequest(
                    0,
                    new ImmediateDateNode,
                    new IntervalNode(0, '0'),
                    new RepetitionsNode(0, '   '),
                    new TextNode(0, ' '),
                    new PayerNumberNode(0, 'foobar'),
                    AmountNode::fromAmount($amount->getWrappedObject()),
                    BankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new TextNode(0, '             ref', '/^.{16}$/'),
                    [new TextNode(0, str_pad('', 11))]
                )
            )
        );
    }
}
