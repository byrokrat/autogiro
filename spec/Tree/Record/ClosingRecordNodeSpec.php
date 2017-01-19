<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree\Record;

use byrokrat\autogiro\Tree\Record\ClosingRecordNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class ClosingRecordNodeSpec extends ObjectBehavior
{
    function let(DateNode $date, TextNode $bgcClear, TextNode $numberOfPosts, TextNode $endVoid)
    {
        $this->beConstructedWith(123, $date, $bgcClear, $numberOfPosts, [$endVoid]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClosingRecordNode::CLASS);
    }

    function it_implements_record_interface()
    {
        $this->shouldHaveType(RecordNode::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('ClosingRecordNode');
    }

    function it_contains_a_line_number()
    {
        $this->getLineNr()->shouldEqual(123);
    }

    function it_contains_a_date($date)
    {
        $this->getChild('date')->shouldEqual($date);
    }

    function it_contains_the_bgc_clearing_nr($bgcClear)
    {
        $this->getChild('bgc_clearing')->shouldEqual($bgcClear);
    }

    function it_contains_record_count($numberOfPosts)
    {
        $this->getChild('nr_of_posts')->shouldEqual($numberOfPosts);
    }

    function it_may_contain_void_ending_nodes($endVoid)
    {
        $this->getChild('end_0')->shouldEqual($endVoid);
    }
}
