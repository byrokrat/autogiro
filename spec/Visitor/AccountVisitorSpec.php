<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\AccountVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\AccountNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\banking\AccountFactory;
use byrokrat\banking\AccountNumber;
use byrokrat\banking\Exception\InvalidAccountNumberException as BankingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccountVisitorSpec extends ObjectBehavior
{
    function let(
        ErrorObject $errorObj,
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

        $this->beConstructedWith($errorObj, $accountFactory, $bankgiroFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AccountVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_fails_on_unvalid_account_number($accountNode, $errorObj)
    {
        $accountNode->getValue()->willReturn('not-valid');
        $this->visitBefore($accountNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_account_numbers($accountNode, $accountNumber, $errorObj)
    {
        $accountNode->getValue()->willReturn('valid');
        $accountNode->setAttribute('account', $accountNumber)->shouldBeCalled();
        $this->visitBefore($accountNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_bankgiro_number($bankgiroNode, $errorObj)
    {
        $bankgiroNode->getValue()->willReturn('not-valid');
        $this->visitBefore($bankgiroNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_bankgiro_numbers($bankgiroNode, $accountNumber, $errorObj)
    {
        $bankgiroNode->getValue()->willReturn('valid');
        $bankgiroNode->setAttribute('account', $accountNumber)->shouldBeCalled();
        $this->visitBefore($bankgiroNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
