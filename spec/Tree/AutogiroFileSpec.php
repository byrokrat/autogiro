<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\Section;
use byrokrat\autogiro\Exception\LogicException;
use PhpSpec\ObjectBehavior;

class AutogiroFileSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AutogiroFile::CLASS);
    }

    function it_is_a_section_node()
    {
        $this->shouldHaveType(Section::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('AutogiroFile');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('AutogiroFile');
    }

    function it_contains_a_layout_name()
    {
        $this->beConstructedWith('some-name');
        $this->getAttribute('layout')->shouldEqual('some-name');
    }
}
