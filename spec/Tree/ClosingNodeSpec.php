<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree;
use PhpSpec\ObjectBehavior;

class ClosingNodeSpec extends ObjectBehavior
{
    const NR_OF_POSTS = 10;
    const LINE_NR = 1;

    function let(\DateTimeImmutable $date)
    {
        $this->beConstructedWith($date, self::NR_OF_POSTS, self::LINE_NR);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Tree\ClosingNode::CLASS);
    }

    function it_implements_node_interface()
    {
        $this->shouldHaveType(Tree\NodeInterface::CLASS);
    }

    function it_accepts_a_visitor(Tree\VisitorInterface $visitor)
    {
        $this->accept($visitor);
        $visitor->visitClosingNode($this)->shouldHaveBeenCalled();
    }

    function it_contains_a_line_number()
    {
        $this->getLineNr()->shouldEqual(self::LINE_NR);
    }

    function it_contains_a_date($date)
    {
        $this->getDate()->shouldEqual($date);
    }

    function it_contains_record_count()
    {
        $this->getNumberOfRecords()->shouldEqual(self::NR_OF_POSTS);
    }
}
