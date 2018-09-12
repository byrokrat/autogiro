<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\AccountVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Obj;
use byrokrat\banking\AccountFactoryInterface;
use byrokrat\banking\AccountNumber;
use byrokrat\banking\Exception\InvalidAccountNumberException as BankingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccountVisitorSpec extends ObjectBehavior
{
    function let(
        ErrorObject $errorObj,
        AccountFactoryInterface $accountFactory,
        AccountFactoryInterface $bankgiroFactory,
        AccountNumber $accountNumber
    ) {
        $this->beConstructedWith($errorObj, $accountFactory, $bankgiroFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AccountVisitor::CLASS);
    }

    function it_fails_on_unvalid_account_number(Node $node, $accountFactory, $errorObj)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild('Object')->willReturn(false);

        $node->getValueFrom('Number')->willReturn('not-valid');
        $accountFactory->createAccount('not-valid')->willThrow(BankingException::CLASS);

        $this->beforeAccount($node);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_account_numbers(
        Node $node,
        AccountNumber $accountNumber,
        $accountFactory
    ) {
        $node->getLineNr()->willReturn(1);
        $node->hasChild('Object')->willReturn(false);

        $node->getValueFrom('Number')->willReturn('valid');
        $accountFactory->createAccount('valid')->willReturn($accountNumber);

        $node->addChild(Argument::that(function (Obj $obj) use ($accountNumber) {
            return $obj->getValue() == $accountNumber->getWrappedObject();
        }))->shouldBeCalled();

        $this->beforeAccount($node);
    }

    function it_does_not_create_account_if_object_exists(Node $node)
    {
        $node->hasChild('Object')->willReturn(true);
        $this->beforeAccount($node);
        $node->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_account_if_number_is_zero(Node $node)
    {
        $node->hasChild('Object')->willReturn(false);
        $node->getValueFrom('Number')->willReturn('0000');
        $this->beforeAccount($node);
        $node->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_bankgiro_number(Node $node, $bankgiroFactory, $errorObj)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild('Object')->willReturn(false);

        $node->getValueFrom('Number')->willReturn('not-valid');
        $bankgiroFactory->createAccount('not-valid')->willThrow(BankingException::CLASS);

        $this->beforePayeeBankgiro($node);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_bankgiro_numbers(
        Node $node,
        AccountNumber $accountNumber,
        $bankgiroFactory
    ) {
        $node->getLineNr()->willReturn(1);
        $node->hasChild('Object')->willReturn(false);

        $node->getValueFrom('Number')->willReturn('valid');
        $bankgiroFactory->createAccount('valid')->willReturn($accountNumber);

        $node->addChild(Argument::that(function (Obj $obj) use ($accountNumber) {
            return $obj->getValue() == $accountNumber->getWrappedObject();
        }))->shouldBeCalled();

        $this->beforePayeeBankgiro($node);
    }

    function it_does_not_create_bankgiro_if_object_exists(Node $node)
    {
        $node->hasChild('Object')->willReturn(true);
        $this->beforePayeeBankgiro($node);
        $node->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_bankgiro_if_number_is_zero(Node $node)
    {
        $node->hasChild('Object')->willReturn(false);
        $node->getValueFrom('Number')->willReturn('0000');
        $this->beforePayeeBankgiro($node);
        $node->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }
}
