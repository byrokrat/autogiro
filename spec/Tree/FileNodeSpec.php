<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\SectionNode;
use byrokrat\autogiro\Exception\LogicException;
use PhpSpec\ObjectBehavior;

class FileNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FileNode::CLASS);
    }

    function it_is_a_section_node()
    {
        $this->shouldHaveType(SectionNode::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('FileNode');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('SectionNode');
    }

    function it_contains_a_layout_name()
    {
        $this->beConstructedWith('some-name');
        $this->getAttribute('layout')->shouldEqual('some-name');
    }
}
