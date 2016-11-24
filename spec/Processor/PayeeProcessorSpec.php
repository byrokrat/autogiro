<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\PayeeProcessor;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\Account\BankgiroNode;
use byrokrat\autogiro\Tree\BgcCustomerNumberNode;
use PhpSpec\ObjectBehavior;

class PayeeProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PayeeProcessor::CLASS);
    }

    function let(
        OpeningNode $openingA,
        BankgiroNode $bgA,
        BgcCustomerNumberNode $custA,
        OpeningNode $openingB,
        BankgiroNode $bgB,
        BgcCustomerNumberNode $custB
    ) {
        $bgA->getValue()->willReturn('A');
        $custA->getValue()->willReturn('A');
        $openingA->getChild('bankgiro')->willReturn($bgA);
        $openingA->getChild('customer_number')->willReturn($custA);
        $openingA->getLineNr()->willReturn(0);

        $bgB->getValue()->willReturn('B');
        $custB->getValue()->willReturn('B');
        $openingB->getChild('bankgiro')->willReturn($bgB);
        $openingB->getChild('customer_number')->willReturn($custB);
        $openingB->getLineNr()->willReturn(0);
    }

    function it_ignores_consistent_payee_info($openingA)
    {
        $this->beforeOpeningNode($openingA);
        $this->beforeOpeningNode($openingA);
        $this->hasErrors()->shouldEqual(false);
    }

    function it_failes_on_inconsistent_payee_info($openingA, $openingB)
    {
        $this->beforeOpeningNode($openingA);
        $this->beforeOpeningNode($openingB);
        $this->hasErrors()->shouldEqual(true);
    }

    function it_ignores_inconsistent_payee_info_in_different_files($openingA, $openingB, FileNode $fileNode)
    {
        $this->beforeOpeningNode($openingA);
        $this->beforeFileNode($fileNode);
        $this->beforeOpeningNode($openingB);
        $this->hasErrors()->shouldEqual(false);
    }
}
