<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Repetitions;
use byrokrat\autogiro\Tree\Text;
use PhpSpec\ObjectBehavior;

class RepetitionsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Repetitions::CLASS);
    }

    function it_implements_the_text_node_interface()
    {
        $this->shouldHaveType(Text::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('Repetitions');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Repetitions');
    }

    function it_contains_a_regexp()
    {
        $this->getValidationRegexp()->shouldEqual('/^([0-9]{3})|( {3})$/');
    }
}
