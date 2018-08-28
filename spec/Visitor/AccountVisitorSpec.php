<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\AccountVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
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

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_fails_on_unvalid_account_number(Node $accountNode, Node $number, $accountFactory, $errorObj)
    {
        $accountNode->getLineNr()->willReturn(1);
        $accountNode->hasChild('Object')->willReturn(false);
        $accountNode->getChild('Number')->willReturn($number);

        $number->getValue()->willReturn('not-valid');
        $accountFactory->createAccount('not-valid')->willThrow(BankingException::CLASS);

        $this->beforeAccount($accountNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_account_numbers(
        Node $accountNode,
        Node $number,
        AccountNumber $accountNumber,
        $accountFactory
    ) {
        $accountNode->getLineNr()->willReturn(1);
        $accountNode->hasChild('Object')->willReturn(false);
        $accountNode->getChild('Number')->willReturn($number);

        $number->getValue()->willReturn('valid');
        $accountFactory->createAccount('valid')->willReturn($accountNumber);

        $accountNode->addChild(Argument::that(function (Obj $obj) use ($accountNumber) {
            return $obj->getValue() == $accountNumber->getWrappedObject();
        }))->shouldBeCalled();

        $this->beforeAccount($accountNode);
    }

    function it_does_not_create_account_if_object_exists(Node $accountNode)
    {
        $accountNode->hasChild('Object')->willReturn(true);
        $this->beforeAccount($accountNode);
        $accountNode->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_account_if_number_is_zero(Node $accountNode, Node $number)
    {
        $accountNode->hasChild('Object')->willReturn(false);
        $accountNode->getChild('Number')->willReturn($number);
        $number->getValue()->willReturn('0000');
        $this->beforeAccount($accountNode);
        $accountNode->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_bankgiro_number(Node $bankgiroNode, Node $number, $bankgiroFactory, $errorObj)
    {
        $bankgiroNode->getLineNr()->willReturn(1);
        $bankgiroNode->hasChild('Object')->willReturn(false);
        $bankgiroNode->getChild('Number')->willReturn($number);

        $number->getValue()->willReturn('not-valid');
        $bankgiroFactory->createAccount('not-valid')->willThrow(BankingException::CLASS);

        $this->beforePayeeBankgiro($bankgiroNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_bankgiro_numbers(
        Node $bankgiroNode,
        Node $number,
        AccountNumber $accountNumber,
        $bankgiroFactory
    ) {
        $bankgiroNode->getLineNr()->willReturn(1);
        $bankgiroNode->hasChild('Object')->willReturn(false);
        $bankgiroNode->getChild('Number')->willReturn($number);

        $number->getValue()->willReturn('valid');
        $bankgiroFactory->createAccount('valid')->willReturn($accountNumber);

        $bankgiroNode->addChild(Argument::that(function (Obj $obj) use ($accountNumber) {
            return $obj->getValue() == $accountNumber->getWrappedObject();
        }))->shouldBeCalled();

        $this->beforePayeeBankgiro($bankgiroNode);
    }

    function it_does_not_create_bankgiro_if_object_exists(Node $bankgiroNode)
    {
        $bankgiroNode->hasChild('Object')->willReturn(true);
        $this->beforePayeeBankgiro($bankgiroNode);
        $bankgiroNode->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_bankgiro_if_number_is_zero(Node $bankgiroNode, Node $number)
    {
        $bankgiroNode->hasChild('Object')->willReturn(false);
        $bankgiroNode->getChild('Number')->willReturn($number);
        $number->getValue()->willReturn('0000');
        $this->beforePayeeBankgiro($bankgiroNode);
        $bankgiroNode->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }
}
