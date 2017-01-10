<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\ContainingVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContainingVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj, Visitor $visitorA, Visitor $visitorB, Node $node)
    {
        $node->getType()->willReturn('');
        $this->beConstructedWith($errorObj, $visitorA, $visitorB);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContainingVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_can_load_visitors_via_constructor($visitorA, $visitorB)
    {
        $this->getVisitors()->shouldEqual([$visitorA, $visitorB]);
    }

    function it_can_load_visitors_via_method($visitorA, $visitorB)
    {
        $this->addVisitor($visitorA);
        $this->getVisitors()->shouldEqual([$visitorA, $visitorB, $visitorA]);
    }

    function it_delegates_visit_before($visitorA, $visitorB, $node)
    {
        $this->visitBefore($node);
        $visitorA->visitBefore($node)->shouldHaveBeenCalled();
        $visitorB->visitBefore($node)->shouldHaveBeenCalled();
    }

    function it_delegates_visit_after($visitorA, $visitorB, $node)
    {
        $this->visitAfter($node);
        $visitorA->visitAfter($node)->shouldHaveBeenCalled();
        $visitorB->visitAfter($node)->shouldHaveBeenCalled();
    }

    function it_throws_exception_on_errors($errorObj, $node)
    {
        $node->getType()->willReturn('FileNode');
        $errorObj->hasErrors()->willReturn(true);
        $errorObj->getErrors()->willReturn(['an error']);

        // TODO fel undantag...
        $this->shouldThrow('\Exception')->duringVisitAfter($node);
    }

    function it_resets_errors($errorObj, $node)
    {
        $node->getType()->willReturn('FileNode');
        $this->visitBefore($node);
        $errorObj->resetErrors()->shouldHaveBeenCalled();
    }
}
