<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\Output;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OutputSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Output::class);
    }

    function it_captures_output()
    {
        $this->write('foo');
        $this->write('bar');
        $this->getContent()->shouldEqual('foobar');
    }
}
