<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\DateTime;
use byrokrat\autogiro\Tree\Date;
use PhpSpec\ObjectBehavior;

class DateTimeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateTime::CLASS);
    }

    function it_is_a_date_node()
    {
        $this->shouldHaveType(Date::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('DateTime');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Date');
    }
}
