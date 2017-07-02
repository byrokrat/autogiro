<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Tree\Record\Request\RequestOpeningRecordNode;
use byrokrat\autogiro\Tree\Record\Request\AcceptMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\CreateMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\DeleteMandateRequestNode;
use byrokrat\autogiro\Tree\Record\Request\RejectMandateRequestNode;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\banking\AccountNumber;
use byrokrat\banking\Bankgiro;
use byrokrat\id\Id;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TreeBuilderSpec extends ObjectBehavior
{
    const BCG_NR = 'bgcNr';
    const BANKGIRO = 'bankgiro';
    const DATE = 'date';

    function let(Bankgiro $bankgiro, \DateTime $date)
    {
        $bankgiro->getNumber()->willReturn(self::BANKGIRO);
        $date->format('Ymd')->willReturn(self::DATE);
        $this->beConstructedWith(self::BCG_NR, $bankgiro, $date);
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

    function a_tree($bankgiro, $date, ...$nodes)
    {
        return new FileNode(
            new LayoutNode(
                $this->an_opening_record_node($bankgiro, $date),
                ...$nodes
            )
        );
    }

    function an_opening_record_node($bankgiro, $date)
    {
        return new RequestOpeningRecordNode(
            0,
            (new DateNode(0, self::DATE))->setAttribute('date', $date->getWrappedObject()),
            new TextNode(0, 'AUTOGIRO'),
            new TextNode(0, str_pad('', 44)),
            new PayeeBgcNumberNode(0, self::BCG_NR),
            (new PayeeBankgiroNode(0, self::BANKGIRO))->setAttribute('account', $bankgiro->getWrappedObject()),
            [new TextNode(0, '  ')]
        );
    }

    function it_builds_create_mandate_trees($bankgiro, $date, AccountNumber $account, Id $id)
    {
        $account->getNumber()->willReturn('account_number');
        $id->__tostring()->willReturn('id_number');

        $this->addCreateMandateRecord('payerNr', $account, $id);

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                $bankgiro,
                $date,
                new CreateMandateRequestNode(
                    0,
                    (new PayeeBankgiroNode(0, self::BANKGIRO))->setAttribute('account', $bankgiro->getWrappedObject()),
                    new PayerNumberNode(0, 'payerNr'),
                    (new AccountNode(0, 'account_number'))->setAttribute('account', $account->getWrappedObject()),
                    (new IdNode(0, 'id_number'))->setAttribute('id', $id->getWrappedObject()),
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
                $bankgiro,
                $date,
                new DeleteMandateRequestNode(
                    0,
                    (new PayeeBankgiroNode(0, self::BANKGIRO))->setAttribute('account', $bankgiro->getWrappedObject()),
                    new PayerNumberNode(0, 'payerNr'),
                    [new TextNode(0, str_pad('', 52))]
                )
            )
        );
    }

    function it_builds_accept_mandate_trees($bankgiro, $date)
    {
        $this->addAcceptMandateRecord('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                $bankgiro,
                $date,
                new AcceptMandateRequestNode(
                    0,
                    (new PayeeBankgiroNode(0, self::BANKGIRO))->setAttribute('account', $bankgiro->getWrappedObject()),
                    new PayerNumberNode(0, 'payerNr'),
                    [new TextNode(0, str_pad('', 52))]
                )
            )
        );
    }

    function it_builds_reject_mandate_trees($bankgiro, $date)
    {
        $this->addRejectMandateRecord('payerNr');

        $this->buildTree()->shouldBeLike(
            $this->a_tree(
                $bankgiro,
                $date,
                new RejectMandateRequestNode(
                    0,
                    (new PayeeBankgiroNode(0, self::BANKGIRO))->setAttribute('account', $bankgiro->getWrappedObject()),
                    new PayerNumberNode(0, 'payerNr'),
                    new TextNode(0, str_pad('', 48)),
                    new TextNode(0, 'AV'),
                    [new TextNode(0, '  ')]
                )
            )
        );
    }
}
