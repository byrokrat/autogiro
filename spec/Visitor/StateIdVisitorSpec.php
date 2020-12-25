<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\StateIdVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Obj;
use byrokrat\id\IdFactoryInterface;
use byrokrat\id\IdInterface;
use byrokrat\id\Exception\RuntimeException as IdException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StateIdVisitorSpec extends ObjectBehavior
{
    function let(
        ErrorObject $errorObj,
        IdFactoryInterface $organizationIdFactory,
        IdFactoryInterface $personalIdFactory,
        IdInterface $id
    ) {
        $this->beConstructedWith($errorObj, $organizationIdFactory, $personalIdFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StateIdVisitor::CLASS);
    }

    function it_does_not_create_id_if_object_is_set(Node $node)
    {
        $node->hasChild(Node::OBJ)->willReturn(true);
        $this->beforeStateId($node);
        $node->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_id_if_value_is_zeros(Node $node)
    {
        $node->hasChild(Node::OBJ)->willReturn(false);
        $node->getValueFrom(Node::NUMBER)->willReturn('00000');
        $this->beforeStateId($node);
        $node->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_organizational_id(Node $node, $organizationIdFactory, $errorObj)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild(Node::OBJ)->willReturn(false);

        $node->getValueFrom(Node::NUMBER)->willReturn('99-not-a-valid-id');
        $organizationIdFactory->createId('-not-a-valid-id')->willThrow(IdException::CLASS);

        $this->beforeStateId($node);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_organizational_ids(Node $node, IdInterface $id, $organizationIdFactory)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild(Node::OBJ)->willReturn(false);

        $node->getValueFrom(Node::NUMBER)->willReturn('00-valid-organization-id');
        $organizationIdFactory->createId('-valid-organization-id')->willReturn($id);

        $node->addChild(Argument::that(function (Obj $obj) use ($id) {
            return $obj->getValue() === $id->getWrappedObject();
        }))->shouldBeCalled();

        $this->beforeStateId($node);
    }

    function it_fails_on_unvalid_personal_id(Node $node, $personalIdFactory, $errorObj)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild(Node::OBJ)->willReturn(false);

        $node->getValueFrom(Node::NUMBER)->willReturn('not-valid');
        $personalIdFactory->createId('not-valid')->willThrow(IdException::CLASS);

        $this->beforeStateId($node);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_personal_ids(Node $node, IdInterface $id, $personalIdFactory)
    {
        $node->getLineNr()->willReturn(1);
        $node->hasChild(Node::OBJ)->willReturn(false);

        $node->getValueFrom(Node::NUMBER)->willReturn('valid');
        $personalIdFactory->createId('valid')->willReturn($id);

        $node->addChild(Argument::that(function (Obj $obj) use ($id) {
            return $obj->getValue() === $id->getWrappedObject();
        }))->shouldBeCalled();

        $this->beforeStateId($node);
    }
}
