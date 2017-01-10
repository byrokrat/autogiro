<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\PayeeProcessor;
use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use PhpSpec\ObjectBehavior;

class PayeeProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PayeeProcessor::CLASS);
    }

    function it_extends_processor()
    {
        $this->shouldHaveType(Processor::CLASS);
    }

    function let(PayeeBankgiroNode $bgA, PayeeBankgiroNode $bgB, PayeeBgcNumberNode $custA, PayeeBgcNumberNode $custB)
    {
        $bgA->getValue()->willReturn('A');

        $bgB->getValue()->willReturn('B');
        $bgB->getLineNr()->willReturn(1);

        $custA->getValue()->willReturn('A');

        $custB->getValue()->willReturn('B');
        $custB->getLineNr()->willReturn(1);
    }

    function it_ignores_consistent_payee_info($bgA, $custA)
    {
        $this->beforePayeeBankgiroNode($bgA);
        $this->beforePayeeBankgiroNode($bgA);

        $this->beforePayeeBgcNumberNode($custA);
        $this->beforePayeeBgcNumberNode($custA);

        $this->hasErrors()->shouldEqual(false);
    }

    function it_failes_on_inconsistent_payee_bankgiro($bgA, $bgB)
    {
        $this->beforePayeeBankgiroNode($bgA);
        $this->beforePayeeBankgiroNode($bgB);
        $this->hasErrors()->shouldEqual(true);
    }

    function it_failes_on_inconsistent_payee_bgc_numbers($custA, $custB)
    {
        $this->beforePayeeBgcNumberNode($custA);
        $this->beforePayeeBgcNumberNode($custB);
        $this->hasErrors()->shouldEqual(true);
    }

    function it_ignores_inconsistent_payee_info_in_different_files($bgA, $bgB, FileNode $fileNode)
    {
        $this->beforePayeeBankgiroNode($bgA);
        $this->beforeFileNode($fileNode);
        $this->beforePayeeBankgiroNode($bgB);
        $this->hasErrors()->shouldEqual(false);
    }
}
