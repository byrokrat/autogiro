<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\ErrorObject;
use PhpSpec\ObjectBehavior;

class ErrorObjectSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ErrorObject::CLASS);
    }

    function it_defaults_to_no_error()
    {
        $this->hasErrors()->shouldEqual(false);
    }

    function it_can_store_errors()
    {
        $this->addError('foo %s %s', 'bar', 'baz');
        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldEqual(['foo bar baz']);
    }

    function it_can_reset_errors()
    {
        $this->addError('foo %s', 'bar');
        $this->resetErrors();
        $this->hasErrors()->shouldEqual(false);
    }
}
