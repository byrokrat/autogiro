<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\TreeBuilder;
use byrokrat\autogiro\Tree\Record\Request\RequestOpeningRecordNode;
use byrokrat\autogiro\Tree\Record\Request\DeleteMandateRequestNode;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\banking\Bankgiro;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TreeBuilderSpec extends ObjectBehavior
{
    function let(Bankgiro $bankgiro, \DateTime $date)
    {
        $bankgiro->getNumber()->willReturn('bankgiro');
        $date->format('Ymd')->willReturn('date');
        $this->beConstructedWith('bgcNr', $bankgiro, $date);
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

    function it_builds_simple_delete_mandate_trees($bankgiro, $date)
    {
        $this->addDeleteMandateRecord('payerNr');
        $this->buildTree()->shouldBeLike(
            new FileNode(
                new LayoutNode(
                    new RequestOpeningRecordNode(
                        0,
                        (new DateNode(0, 'date'))->setAttribute('date', $date->getWrappedObject()),
                        new TextNode(0, 'AUTOGIRO'),
                        new TextNode(0, str_pad('', 44)),
                        new PayeeBgcNumberNode(0, 'bgcNr'),
                        (new PayeeBankgiroNode(0, 'bankgiro'))->setAttribute('account', $bankgiro->getWrappedObject()),
                        [new TextNode(0, '  ')]
                    ),
                    new DeleteMandateRequestNode(
                        0,
                        (new PayeeBankgiroNode(0, 'bankgiro'))->setAttribute('account', $bankgiro->getWrappedObject()),
                        new PayerNumberNode(0, 'payerNr'),
                        [new TextNode(0, str_pad('', 52))]
                    )
                )
            )
        );
    }
}
