<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\AccountProcessor;
use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\banking\AccountFactory;
use byrokrat\banking\AccountNumber;
use byrokrat\banking\Exception\InvalidAccountNumberException as BankingException;
use PhpSpec\ObjectBehavior;

class AccountProcessorSpec extends ObjectBehavior
{
    function let(
        AccountFactory $accountFactory,
        AccountFactory $bankgiroFactory,
        AccountNode $accountNode,
        PayeeBankgiroNode $bankgiroNode,
        AccountNumber $accountNumber
    ) {
        $accountFactory->createAccount('not-valid')->willThrow(BankingException::CLASS);
        $accountFactory->createAccount('valid')->willReturn($accountNumber);

        $bankgiroFactory->createAccount('not-valid')->willThrow(BankingException::CLASS);
        $bankgiroFactory->createAccount('valid')->willReturn($accountNumber);

        $accountNode->getLineNr()->willReturn(1);
        $accountNode->getType()->willReturn('AccountNode');

        $bankgiroNode->getLineNr()->willReturn(1);
        $bankgiroNode->getType()->willReturn('PayeeBankgiroNode');

        $this->beConstructedWith($accountFactory, $bankgiroFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AccountProcessor::CLASS);
    }

    function it_extends_processor()
    {
        $this->shouldHaveType(Processor::CLASS);
    }

    function it_fails_on_unvalid_account_number($accountNode)
    {
        $accountNode->getValue()->willReturn('not-valid');
        $this->visitBefore($accountNode);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_creates_valid_account_numbers($accountNode, $accountNumber)
    {
        $accountNode->getValue()->willReturn('valid');
        $accountNode->setAttribute('account', $accountNumber)->shouldBeCalled();
        $this->visitBefore($accountNode);
        $this->getErrors()->shouldHaveCount(0);
    }

    function it_fails_on_unvalid_bankgiro_number($bankgiroNode)
    {
        $bankgiroNode->getValue()->willReturn('not-valid');
        $this->visitBefore($bankgiroNode);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_creates_valid_bankgiro_numbers($bankgiroNode, $accountNumber)
    {
        $bankgiroNode->getValue()->willReturn('valid');
        $bankgiroNode->setAttribute('account', $accountNumber)->shouldBeCalled();
        $this->visitBefore($bankgiroNode);
        $this->getErrors()->shouldHaveCount(0);
    }
}
