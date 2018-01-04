<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Writer\IntervalFormatter;
use byrokrat\autogiro\Writer\RepititionsFormatter;
use byrokrat\autogiro\Layouts;
use byrokrat\autogiro\Tree\Record\Request\RequestOpeningRecordNode;
use byrokrat\autogiro\Tree\Record\Request\AcceptDigitalMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\CreateMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\DeleteMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\RejectDigitalMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\UpdateMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\IncomingTransactionRequestNode;
use byrokrat\autogiro\Tree\Record\Request\OutgoingTransactionRequestNode;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\ImmediateDateNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
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
        $this->addDeleteMandateRecord('payerNr');
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
        return new RequestOpeningRecordNode(
            0,
            DateNode::fromDate($date->getWrappedObject()),
            new TextNode(0, 'AUTOGIRO'),
            new TextNode(0, str_pad('', 44)),
            new PayeeBgcNumberNode(0, self::BCG_NR),
            PayeeBankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
            [new TextNode(0, '  ')]
        );
    }

    function it_builds_create_mandate_trees($bankgiro, $date, AccountNumber $account, IdInterface $id)
    {
        $account->getNumber()->willReturn('account_number');
        $id->__tostring()->willReturn('id_number');

        $this->addCreateMandateRecord('payerNr', $account, $id);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_MANDATE_REQUEST,
                $bankgiro,
                $date,
                new CreateMandateRequestNode(
                    0,
                    PayeeBankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
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
        $this->addDeleteMandateRecord('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_MANDATE_REQUEST,
                $bankgiro,
                $date,
                new DeleteMandateRequestNode(
                    0,
                    PayeeBankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new PayerNumberNode(0, 'payerNr'),
                    [new TextNode(0, str_pad('', 52))]
                )
            )
        );
    }

    function it_builds_accept_mandate_trees($bankgiro, $date)
    {
        $this->addAcceptDigitalMandateRecord('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_MANDATE_REQUEST,
                $bankgiro,
                $date,
                new AcceptDigitalMandateRequestNode(
                    0,
                    PayeeBankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new PayerNumberNode(0, 'payerNr'),
                    [new TextNode(0, str_pad('', 52))]
                )
            )
        );
    }

    function it_builds_reject_mandate_trees($bankgiro, $date)
    {
        $this->addRejectDigitalMandateRecord('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_MANDATE_REQUEST,
                $bankgiro,
                $date,
                new RejectDigitalMandateRequestNode(
                    0,
                    PayeeBankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
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
        $this->addUpdateMandateRecord('foo', 'bar');

        $payeeBgNode = PayeeBankgiroNode::fromBankgiro($bankgiro->getWrappedObject());

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_MANDATE_REQUEST,
                $bankgiro,
                $date,
                new UpdateMandateRequestNode(
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

    function it_builds_incoming_transaction_trees(SEK $amount, $bankgiro, $date, $intervalFormatter, $repsFormatter)
    {
        $intervalFormatter->format(0)->shouldBeCalled()->willReturn('formatted_interval');
        $repsFormatter->format(1)->shouldBeCalled()->willReturn('formatted_repititions');
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addIncomingTransactionRecord('foobar', $amount, $date, 'ref', 0, 1);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_PAYMENT_REQUEST,
                $bankgiro,
                $date,
                new IncomingTransactionRequestNode(
                    0,
                    DateNode::fromDate($date->getWrappedObject()),
                    new IntervalNode(0, 'formatted_interval'),
                    new RepetitionsNode(0, 'formatted_repititions'),
                    new TextNode(0, ' '),
                    new PayerNumberNode(0, 'foobar'),
                    AmountNode::fromAmount($amount->getWrappedObject()),
                    PayeeBankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new TextNode(0, '             ref', '/^.{16}$/'),
                    [new TextNode(0, str_pad('', 11))]
                )
            )
        );
    }

    function it_builds_outgoing_transaction_trees(SEK $amount, $bankgiro, $date, $intervalFormatter, $repsFormatter)
    {
        $intervalFormatter->format(0)->shouldBeCalled()->willReturn('formatted_interval');
        $repsFormatter->format(1)->shouldBeCalled()->willReturn('formatted_repititions');
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addOutgoingTransactionRecord('foobar', $amount, $date, 'ref', 0, 1);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_PAYMENT_REQUEST,
                $bankgiro,
                $date,
                new OutgoingTransactionRequestNode(
                    0,
                    DateNode::fromDate($date->getWrappedObject()),
                    new IntervalNode(0, 'formatted_interval'),
                    new RepetitionsNode(0, 'formatted_repititions'),
                    new TextNode(0, ' '),
                    new PayerNumberNode(0, 'foobar'),
                    AmountNode::fromAmount($amount->getWrappedObject()),
                    PayeeBankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new TextNode(0, '             ref', '/^.{16}$/'),
                    [new TextNode(0, str_pad('', 11))]
                )
            )
        );
    }

    function it_builds_immediate_incoming_transaction_trees(SEK $amount, $bankgiro, $date)
    {
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addImmediateIncomingTransactionRecord('foobar', $amount, 'ref');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_PAYMENT_REQUEST,
                $bankgiro,
                $date,
                new IncomingTransactionRequestNode(
                    0,
                    new ImmediateDateNode,
                    new IntervalNode(0, '0'),
                    new RepetitionsNode(0, '   '),
                    new TextNode(0, ' '),
                    new PayerNumberNode(0, 'foobar'),
                    AmountNode::fromAmount($amount->getWrappedObject()),
                    PayeeBankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new TextNode(0, '             ref', '/^.{16}$/'),
                    [new TextNode(0, str_pad('', 11))]
                )
            )
        );
    }

    function it_builds_immediate_outgoing_transaction_trees(SEK $amount, $bankgiro, $date)
    {
        $amount->getSignalString()->shouldBeCalled()->willReturn('formatted_amount');

        $this->addImmediateOutgoingTransactionRecord('foobar', $amount, 'ref');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                Layouts::LAYOUT_PAYMENT_REQUEST,
                $bankgiro,
                $date,
                new OutgoingTransactionRequestNode(
                    0,
                    new ImmediateDateNode,
                    new IntervalNode(0, '0'),
                    new RepetitionsNode(0, '   '),
                    new TextNode(0, ' '),
                    new PayerNumberNode(0, 'foobar'),
                    AmountNode::fromAmount($amount->getWrappedObject()),
                    PayeeBankgiroNode::fromBankgiro($bankgiro->getWrappedObject()),
                    new TextNode(0, '             ref', '/^.{16}$/'),
                    [new TextNode(0, str_pad('', 11))]
                )
            )
        );
    }
}
