<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\IdProcessor;
use byrokrat\autogiro\Tree\Id\OrganizationIdNode;
use byrokrat\autogiro\Tree\Id\PersonalIdNode;
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
        OrganizationIdNode $organizationIdNode,
        PersonalIdNode $personalIdNode,
        Id $id
    ) {
        $organizationIdFactory->create('not-valid')->willThrow(IdException::CLASS);
        $organizationIdFactory->create('valid')->willReturn($id);

        $personalIdFactory->create('not-valid')->willThrow(IdException::CLASS);
        $personalIdFactory->create('valid')->willReturn($id);

        $organizationIdNode->getLineNr()->willReturn(1);
        $personalIdNode->getLineNr()->willReturn(1);

        $this->beConstructedWith($organizationIdFactory, $personalIdFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IdProcessor::CLASS);
    }

    function it_fails_on_unvalid_organizational_id(OrganizationIdNode $organizationIdNode)
    {
        $organizationIdNode->getValue()->willReturn('not-valid');
        $this->beforeOrganizationIdNode($organizationIdNode);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_creates_valid_organizational_ids(OrganizationIdNode $organizationIdNode, Id $id)
    {
        $organizationIdNode->getValue()->willReturn('valid');
        $organizationIdNode->setAttribute('id', $id)->shouldBeCalled();
        $this->beforeOrganizationIdNode($organizationIdNode);
        $this->getErrors()->shouldHaveCount(0);
    }

    function it_fails_on_unvalid_personal_id(PersonalIdNode $personalIdNode)
    {
        $personalIdNode->getValue()->willReturn('not-valid');
        $this->beforePersonalIdNode($personalIdNode);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_creates_valid_personal_ids(PersonalIdNode $personalIdNode, Id $id)
    {
        $personalIdNode->getValue()->willReturn('valid');
        $personalIdNode->setAttribute('id', $id)->shouldBeCalled();
        $this->beforePersonalIdNode($personalIdNode);
        $this->getErrors()->shouldHaveCount(0);
    }
}
