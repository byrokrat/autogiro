<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\VisitorFactory;
use byrokrat\autogiro\Visitor\VisitorInterface;
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
        $this->createVisitors()->shouldHaveType(VisitorInterface::CLASS);
    }

    function it_creates_visitors_with_no_external_visitors()
    {
        $this->createVisitors(VisitorFactory::VISITOR_IGNORE_OBJECTS)->shouldNotBeLike(
            $this->createVisitors()
        );
    }

    function it_creates_visitors_without_strict_validation()
    {
        $this->createVisitors(VisitorFactory::VISITOR_IGNORE_STRICT_VALIDATION)->shouldNotBeLike(
            $this->createVisitors()
        );
    }
}
