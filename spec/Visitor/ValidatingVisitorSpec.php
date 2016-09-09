<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\ValidatingVisitor;
use byrokrat\autogiro\Exception;
use byrokrat\autogiro\Tree\VisitorInterface;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\ClosingNode;
use byrokrat\autogiro\Tree\MandateResponseNode;
use byrokrat\banking\Bankgiro;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidatingVisitorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ValidatingVisitor::CLASS);
    }

    function it_implements_visitor_interface()
    {
        $this->shouldHaveType(VisitorInterface::CLASS);
    }

    function it_should_default_to_no_errors()
    {
        $this->hasErrors()->shouldEqual(false);
        $this->getErrors()->shouldHaveCount(0);
    }

    function it_fails_on_missmatching_dates(LayoutNode $node, OpeningNode $opening, ClosingNode $closing)
    {
        $opening->getDate()->willReturn(new \DateTime('2010'));
        $closing->getDate()->willReturn(new \DateTime('2011'));
        $closing->getNumberOfRecords()->willReturn(0);
        $closing->getLineNr()->willReturn(1);
        $node->getOpeningNode()->willReturn($opening);
        $node->getClosingNode()->willReturn($closing);
        $node->getContentNodes()->willReturn([]);

        $this->visitLayoutNode($node);

        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_record_count(LayoutNode $node, OpeningNode $opening, ClosingNode $closing)
    {
        $date = new \DateTime;
        $opening->getDate()->willReturn($date);
        $closing->getDate()->willReturn($date);
        $closing->getNumberOfRecords()->willReturn(1);
        $closing->getLineNr()->willReturn(1);
        $node->getOpeningNode()->willReturn($opening);
        $node->getClosingNode()->willReturn($closing);
        $node->getContentNodes()->willReturn([]);

        $this->visitLayoutNode($node);

        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_missmatching_bg(OpeningNode $opening, MandateResponseNode $mandate, Bankgiro $a, Bankgiro $b)
    {
        $a->__toString()->willReturn('');
        $b->__toString()->willReturn('');

        $opening->getBankgiro()->willReturn($a);
        $mandate->getBankgiro()->willReturn($b);
        $mandate->getLineNr()->willReturn(1);

        $this->visitOpeningNode($opening);
        $this->visitMandateResponseNode($mandate);

        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_can_reset_errors(OpeningNode $opening, MandateResponseNode $mandate, Bankgiro $a, Bankgiro $b)
    {
        $a->__toString()->willReturn('');
        $b->__toString()->willReturn('');

        $opening->getBankgiro()->willReturn($a);
        $mandate->getBankgiro()->willReturn($b);
        $mandate->getLineNr()->willReturn(1);

        $this->visitOpeningNode($opening);
        $this->visitMandateResponseNode($mandate);

        $this->hasErrors()->shouldEqual(true);

        $this->reset();

        $this->hasErrors()->shouldEqual(false);
        $this->getErrors()->shouldHaveCount(0);
    }
}
