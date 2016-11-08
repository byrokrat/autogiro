<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\FileProcessor;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\BankgiroNode;
use byrokrat\autogiro\Tree\BgcCustomerNumberNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FileProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FileProcessor::CLASS);
    }

    function it_writes_attributes(
        FileNode $fileNode,
        LayoutNode $layoutNode,
        OpeningNode $openingNode,
        BankgiroNode $bankgiroNode,
        BgcCustomerNumberNode $bgcCustNrNode
    ) {
        $bankgiroNode->getValue()->willReturn('111-111');
        $openingNode->getChild('bankgiro')->willReturn($bankgiroNode);

        $bgcCustNrNode->getValue()->willReturn('12345');
        $openingNode->getChild('customer_number')->willReturn($bgcCustNrNode);

        $openingNode->getAttribute('layout_name')->willReturn('name');

        $layoutNode->getChild('opening')->willReturn($openingNode);

        $fileNode->getChildren()->willReturn([$layoutNode]);

        $fileNode->setAttribute('customer_number', '12345')->shouldBeCalled();
        $fileNode->setAttribute('bankgiro', '111-111')->shouldBeCalled();
        $fileNode->setAttribute('layout_ids', ['name'])->shouldBeCalled();

        $this->afterFileNode($fileNode);
    }
}
