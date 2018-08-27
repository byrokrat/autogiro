<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\StateIdVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
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

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_does_not_create_id_if_object_is_set(Node $container)
    {
        $container->hasChild('Object')->willReturn(true);
        $this->beforeStateId($container);
        $container->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_create_id_if_value_is_zeros(Node $container, Node $number)
    {
        $container->hasChild('Object')->willReturn(false);
        $container->getChild('Number')->willReturn($number);
        $number->getValue()->willReturn('00000');
        $this->beforeStateId($container);
        $container->addChild(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_unvalid_organizational_id(Node $container, Node $number, $organizationIdFactory, $errorObj)
    {
        $container->getLineNr()->willReturn(1);
        $container->hasChild('Object')->willReturn(false);
        $container->getChild('Number')->willReturn($number);

        $number->getValue()->willReturn('99-not-a-valid-id');
        $organizationIdFactory->createId('-not-a-valid-id')->willThrow(IdException::CLASS);

        $this->beforeStateId($container);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_organizational_ids(Node $container, Node $number, IdInterface $id, $organizationIdFactory)
    {
        $container->getLineNr()->willReturn(1);
        $container->hasChild('Object')->willReturn(false);
        $container->getChild('Number')->willReturn($number);

        $number->getValue()->willReturn('00-valid-organization-id');
        $organizationIdFactory->createId('-valid-organization-id')->willReturn($id);

        $container->addChild(Argument::that(function (Obj $obj) use ($id) {
            return $obj->getValue() === $id->getWrappedObject();
        }))->shouldBeCalled();

        $this->beforeStateId($container);
    }

    function it_fails_on_unvalid_personal_id(Node $container, Node $number, $personalIdFactory, $errorObj)
    {
        $container->getLineNr()->willReturn(1);
        $container->hasChild('Object')->willReturn(false);
        $container->getChild('Number')->willReturn($number);

        $number->getValue()->willReturn('not-valid');
        $personalIdFactory->createId('not-valid')->willThrow(IdException::CLASS);

        $this->beforeStateId($container);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_creates_valid_personal_ids(Node $container, Node $number, IdInterface $id, $personalIdFactory)
    {
        $container->getLineNr()->willReturn(1);
        $container->hasChild('Object')->willReturn(false);
        $container->getChild('Number')->willReturn($number);

        $number->getValue()->willReturn('valid');
        $personalIdFactory->createId('valid')->willReturn($id);

        $container->addChild(Argument::that(function (Obj $obj) use ($id) {
            return $obj->getValue() === $id->getWrappedObject();
        }))->shouldBeCalled();

        $this->beforeStateId($container);
    }
}
