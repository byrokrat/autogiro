<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Xml;

use byrokrat\autogiro\Xml\Stringifier;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StringifierSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Stringifier::CLASS);
    }

    function it_casts_scalars()
    {
        $this->stringify(1)->shouldEqual('1');
    }

    function it_casts_dates()
    {
        $this->stringify(new \DateTime('20180101'))->shouldEqual('2018-01-01');
    }

    function it_casts_arrays()
    {
        $this->stringify([])->shouldEqual(Stringifier::ARRAY_VALUE);
    }

    function it_casts_objects_that_implement_tostring()
    {
        $obj = new class {
            function __toString()
            {
                return 'foobar';
            }
        };

        $this->stringify($obj)->shouldEqual('foobar');
    }

    function it_casts_objects_that_does_not_implement_tostring()
    {
        $obj = new class {
        };

        $this->stringify($obj)->shouldEqual(Stringifier::OBJECT_VALUE);
    }

    function it_casts_null()
    {
        $this->stringify(null)->shouldEqual('');
    }
}
