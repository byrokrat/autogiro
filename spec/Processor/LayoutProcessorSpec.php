<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\LayoutProcessor;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\ClosingNode;
use byrokrat\autogiro\Tree\Date\DateNode;
use PhpSpec\ObjectBehavior;

class LayoutProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LayoutProcessor::CLASS);
    }

    function it_fails_on_missmatching_dates(
        LayoutNode $node,
        OpeningNode $opening,
        ClosingNode $closing,
        DateNode $dateA,
        DateNode $dateB
    ) {
        $dateA->getValue()->willReturn('2010');
        $dateB->getValue()->willReturn('2011');
        $opening->getChild('date')->willReturn($dateA);
        $closing->getChild('date')->willReturn($dateB);
        $closing->getAttribute('nr_of_posts')->willReturn(0);
        $closing->getLineNr()->willReturn(1);
        $node->getChild('opening')->willReturn($opening);
        $node->getChild('closing')->willReturn($closing);
        $node->getChildren()->willReturn([$opening, $closing]);

        $this->afterLayoutNode($node);

        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_record_count(
        LayoutNode $node,
        OpeningNode $opening,
        ClosingNode $closing,
        DateNode $date
    ) {
        $date->getValue()->willReturn('2010');
        $opening->getChild('date')->willReturn($date);
        $closing->getChild('date')->willReturn($date);
        $closing->getAttribute('nr_of_posts')->willReturn(1);
        $closing->getLineNr()->willReturn(1);
        $node->getChild('opening')->willReturn($opening);
        $node->getChild('closing')->willReturn($closing);
        $node->getChildren()->willReturn([$opening, $closing]);

        $this->afterLayoutNode($node);

        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldHaveCount(1);
    }
}
