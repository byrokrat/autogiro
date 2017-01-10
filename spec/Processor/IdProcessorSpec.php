<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\IdProcessor;
use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\IdNode;
use byrokrat\id\OrganizationIdFactory;
use byrokrat\id\PersonalIdFactory;
use byrokrat\id\Id;
use byrokrat\id\Exception\RuntimeException as IdException;
use PhpSpec\ObjectBehavior;

class IdProcessorSpec extends ObjectBehavior
{
    function let(
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

        $this->beConstructedWith($organizationIdFactory, $personalIdFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IdProcessor::CLASS);
    }

    function it_extends_processor()
    {
        $this->shouldHaveType(Processor::CLASS);
    }

    function it_fails_on_unvalid_organizational_id($idNode)
    {
        $idNode->getValue()->willReturn('99-not-valid');
        $this->beforeIdNode($idNode);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_creates_valid_organizational_ids($idNode, $id)
    {
        $idNode->getValue()->willReturn('00-valid');
        $idNode->setAttribute('id', $id)->shouldBeCalled();
        $this->beforeIdNode($idNode);
        $this->getErrors()->shouldHaveCount(0);
    }

    function it_fails_on_unvalid_personal_id($idNode)
    {
        $idNode->getValue()->willReturn('19-not-valid');
        $this->beforeIdNode($idNode);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_creates_valid_personal_ids($idNode, $id)
    {
        $idNode->getValue()->willReturn('20-valid');
        $idNode->setAttribute('id', $id)->shouldBeCalled();
        $this->beforeIdNode($idNode);
        $this->getErrors()->shouldHaveCount(0);
    }
}
