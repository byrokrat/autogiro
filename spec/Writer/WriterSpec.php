<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\Writer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WriterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Writer::CLASS);
    }
}
