<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\AccountVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Account;
use byrokrat\autogiro\Tree\ReferredAccount;
use byrokrat\autogiro\Tree\PayeeBankgiro;
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
        AccountNumber $accountNumber
    ) {
        $accountFactory->createAccount('not-valid')->willThrow(BankingException::CLASS);
        $accountFactory->createAccount('valid')->willReturn($accountNumber);
        $bankgiroFactory->createAccount('not-valid')->willThrow(BankingException::CLASS);
        $bankgiroFactory->createAccount('valid')->willReturn($accountNumber);
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

    function it_fails_on_unvalid_account_number(Account $accountNode, $errorObj)
    {
        $accountNode->hasAttribute('account')->willReturn(false);
        $accountNode->getValue()->willReturn('not-valid');
        $accountNode->getLineNr()->willReturn(1);
        $this->beforeAccount($accountNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_account_numbers(Account $accountNode, $accountNumber, $errorObj)
    {
        $accountNode->hasAttribute('account')->willReturn(false);
        $accountNode->getValue()->willReturn('valid');
        $accountNode->setAttribute('account', $accountNumber)->shouldBeCalled();
        $this->beforeAccount($accountNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_account_if_attr_is_set(Account $accountNode)
    {
        $accountNode->hasAttribute('account')->willReturn(true);
        $accountNode->getValue()->willReturn('');
        $this->beforeAccount($accountNode);
        $accountNode->setAttribute('account', Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_creates_valid_referred_account_numbers(ReferredAccount $accountNode, $accountNumber, $errorObj)
    {
        $accountNode->hasAttribute('account')->willReturn(false);
        $accountNode->getValue()->willReturn('');
        $accountNode->hasAttribute('referred_value')->willReturn(true);
        $accountNode->getAttribute('referred_value')->willReturn('valid');
        $accountNode->setAttribute('account', $accountNumber)->shouldBeCalled();
        $this->beforeReferredAccount($accountNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_referred_account_if_attr_is_not_set(ReferredAccount $accountNode)
    {
        $accountNode->hasAttribute('referred_value')->willReturn(false);
        $this->beforeReferredAccount($accountNode);
        $accountNode->setAttribute('account', Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_bankgiro_number(PayeeBankgiro $bankgiroNode, $errorObj)
    {
        $bankgiroNode->hasAttribute('account')->willReturn(false);
        $bankgiroNode->getValue()->willReturn('not-valid');
        $bankgiroNode->getLineNr()->willReturn(1);
        $this->beforePayeeBankgiro($bankgiroNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_bankgiro_numbers(PayeeBankgiro $bankgiroNode, $accountNumber, $errorObj)
    {
        $bankgiroNode->hasAttribute('account')->willReturn(false);
        $bankgiroNode->getValue()->willReturn('valid');
        $bankgiroNode->setAttribute('account', $accountNumber)->shouldBeCalled();
        $this->beforePayeeBankgiro($bankgiroNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_bankgiro_if_attr_is_set(PayeeBankgiro $bankgiroNode)
    {
        $bankgiroNode->hasAttribute('account')->willReturn(true);
        $bankgiroNode->getValue()->willReturn('');
        $this->beforePayeeBankgiro($bankgiroNode);
        $bankgiroNode->setAttribute('account', Argument::any())->shouldNotHaveBeenCalled();
    }
}
