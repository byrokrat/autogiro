<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\IdVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\id\OrganizationIdFactory;
use byrokrat\id\PersonalIdFactory;
use byrokrat\id\Id;
use byrokrat\id\Exception\RuntimeException as IdException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IdVisitorSpec extends ObjectBehavior
{
    function let(
        ErrorObject $errorObj,
        OrganizationIdFactory $organizationIdFactory,
        PersonalIdFactory $personalIdFactory,
        IdNode $idNode,
        Id $id
    ) {
        $organizationIdFactory->create('-not-valid')->willThrow(IdException::CLASS);
        $organizationIdFactory->create('-valid')->willReturn($id);

        $personalIdFactory->create('19-not-valid')->willThrow(IdException::CLASS);
        $personalIdFactory->create('20-valid')->willReturn($id);

        $idNode->getLineNr()->willReturn(1);

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

    function it_fails_on_unvalid_organizational_id($idNode, $errorObj)
    {
        $idNode->getValue()->willReturn('99-not-valid');
        $this->beforeIdNode($idNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_organizational_ids($idNode, $id, $errorObj)
    {
        $idNode->getValue()->willReturn('00-valid');
        $idNode->setAttribute('id', $id)->shouldBeCalled();
        $this->beforeIdNode($idNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_personal_id($idNode, $errorObj)
    {
        $idNode->getValue()->willReturn('19-not-valid');
        $this->beforeIdNode($idNode);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_personal_ids($idNode, $id, $errorObj)
    {
        $idNode->getValue()->willReturn('20-valid');
        $idNode->setAttribute('id', $id)->shouldBeCalled();
        $this->beforeIdNode($idNode);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
