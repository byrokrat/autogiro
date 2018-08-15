<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\IdVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\id\IdFactoryInterface;
use byrokrat\id\IdInterface;
use byrokrat\id\Exception\RuntimeException as IdException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IdVisitorSpec extends ObjectBehavior
{
    function let(
        ErrorObject $errorObj,
        IdFactoryInterface $organizationIdFactory,
        IdFactoryInterface $personalIdFactory,
        IdInterface $id
    ) {
        $organizationIdFactory->createId('-not-valid')->willThrow(IdException::CLASS);
        $organizationIdFactory->createId('-valid')->willReturn($id);
        $personalIdFactory->createId('19-not-valid')->willThrow(IdException::CLASS);
        $personalIdFactory->createId('20-valid')->willReturn($id);
        $this->beConstructedWith($errorObj, $organizationIdFactory, $personalIdFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IdVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_fails_on_unvalid_organizational_id(IdNode $idNode, $errorObj)
    {
        $idNode->hasAttribute('id')->willReturn(false);
        $idNode->getValue()->willReturn('99-not-valid');
        $idNode->getLineNr()->willReturn(1);
        $this->beforeIdNode($idNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_organizational_ids(IdNode $idNode, $id, $errorObj)
    {
        $idNode->hasAttribute('id')->willReturn(false);
        $idNode->getValue()->willReturn('00-valid');
        $idNode->setAttribute('id', $id)->shouldBeCalled();
        $this->beforeIdNode($idNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_personal_id(IdNode $idNode, $errorObj)
    {
        $idNode->hasAttribute('id')->willReturn(false);
        $idNode->getValue()->willReturn('19-not-valid');
        $idNode->getLineNr()->willReturn(1);
        $this->beforeIdNode($idNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_personal_ids(IdNode $idNode, $id, $errorObj)
    {
        $idNode->hasAttribute('id')->willReturn(false);
        $idNode->getValue()->willReturn('20-valid');
        $idNode->setAttribute('id', $id)->shouldBeCalled();
        $this->beforeIdNode($idNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_id_if_attr_is_set(IdNode $idNode)
    {
        $idNode->hasAttribute('id')->willReturn(true);
        $this->beforeIdNode($idNode);
        $idNode->setAttribute('id', Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_id_if_value_is_zeros(IdNode $idNode)
    {
        $idNode->hasAttribute('id')->willReturn(false);
        $idNode->getValue()->willReturn('00000');
        $this->beforeIdNode($idNode);
        $idNode->setAttribute('id', Argument::any())->shouldNotHaveBeenCalled();
    }
}
