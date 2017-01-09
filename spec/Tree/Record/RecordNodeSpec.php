<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record;

use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class RecordNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf(ConcreteRecordNode::CLASS);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }


    function it_contains_a_line_number()
    {
        $this->beConstructedWith(123);
        $this->getLineNr()->shouldEqual(123);
    }

    function it_may_contain_void_ending_nodes(TextNode $end1, TextNode $end2)
    {
        $this->beConstructedWith(0, [$end1, $end2]);
        $this->getChild('end_0')->shouldEqual($end1);
        $this->getChild('end_1')->shouldEqual($end2);
    }
}

class ConcreteRecordNode extends RecordNode
{
}
