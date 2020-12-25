<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\NumberVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Number;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NumberVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NumberVisitor::CLASS);
    }

    function it_captures_invalid_nodes(Number $node, $errorObj)
    {
        $node->getValue()->willReturn('foo');
        $node->getLineNr()->willReturn(1);
        $node->getName()->willReturn('bar');
        $this->beforeNumber($node);
        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_ignores_nodes_with_valid_content(Number $node, $errorObj)
    {
        $node->getValue()->willReturn('0000123');
        $this->beforeNumber($node);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_ignores_empty_nodes(Number $node, $errorObj)
    {
        $node->getValue()->willReturn('');
        $this->beforeNumber($node);
        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
