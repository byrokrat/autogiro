<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\VisitorFactory;
use byrokrat\autogiro\Visitor\Visitor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class VisitorFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(VisitorFactory::CLASS);
    }

    function it_creates_visitors()
    {
        $this->createVisitors()->shouldHaveType(Visitor::CLASS);
    }

    function it_creates_visitors_with_no_external_visitors()
    {
        $this->createVisitors(VisitorFactory::VISITOR_IGNORE_EXTERNAL)->shouldNotBeLike(
            $this->createVisitors()->shouldHaveType(Visitor::CLASS)
        );
    }
}
