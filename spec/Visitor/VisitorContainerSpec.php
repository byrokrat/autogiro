<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\VisitorContainer;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Visitor\VisitorInterface;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Exception\TreeException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VisitorContainerSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(VisitorContainer::CLASS);
    }

    function it_can_load_visitors(VisitorInterface $visitorA, VisitorInterface $visitorB)
    {
        $this->addVisitor($visitorA);
        $this->addVisitor($visitorB);
        $this->getVisitors()->shouldEqual([$visitorA, $visitorB]);
    }

    function it_delegates_visit_before(VisitorInterface $visitorA, VisitorInterface $visitorB, Node $node)
    {
        $this->addVisitor($visitorA);
        $this->addVisitor($visitorB);
        $node->getName()->willReturn('');
        $node->getType()->willReturn('');
        $this->visitBefore($node);
        $visitorA->visitBefore($node)->shouldHaveBeenCalled();
        $visitorB->visitBefore($node)->shouldHaveBeenCalled();
    }

    function it_delegates_visit_after(VisitorInterface $visitorA, VisitorInterface $visitorB, Node $node)
    {
        $this->addVisitor($visitorA);
        $this->addVisitor($visitorB);
        $node->getName()->willReturn('');
        $node->getType()->willReturn('');
        $this->visitAfter($node);
        $visitorA->visitAfter($node)->shouldHaveBeenCalled();
        $visitorB->visitAfter($node)->shouldHaveBeenCalled();
    }

    function it_throws_exception_on_errors($errorObj, Node $node)
    {
        $node->getName()->willReturn('AutogiroFile');
        $node->getType()->willReturn('');
        $errorObj->hasErrors()->willReturn(true);
        $errorObj->getErrors()->willReturn(['an error']);
        $this->shouldThrow(TreeException::CLASS)->duringVisitAfter($node);
    }

    function it_resets_errors($errorObj, Node $node)
    {
        $node->getName()->willReturn('AutogiroFile');
        $node->getType()->willReturn('');
        $this->visitBefore($node);
        $errorObj->resetErrors()->shouldHaveBeenCalled();
    }
}
