<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\AutogiroFile;
use byrokrat\autogiro\Tree\Container;
use byrokrat\autogiro\Tree\Text;
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

    function it_is_a_container_node()
    {
        $this->shouldHaveType(Container::CLASS);
    }

    function it_contains_a_name()
    {
        $this->beConstructedWith('custom-name');
        $this->getName()->shouldEqual('custom-name');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('AutogiroFile');
    }
}
