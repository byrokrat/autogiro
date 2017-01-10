<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ErrorAwareVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_is_a_visitor()
    {
        $this->shouldHaveType(Visitor::CLASS);
    }

    function it_contains_an_error_object($errorObj)
    {
        $this->getErrorObject()->shouldEqual($errorObj);
    }
}
