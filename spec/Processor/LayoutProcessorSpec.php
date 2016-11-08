<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\LayoutProcessor;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\OpeningNode;
use byrokrat\autogiro\Tree\ClosingNode;
use PhpSpec\ObjectBehavior;

class LayoutProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LayoutProcessor::CLASS);
    }

    function it_fails_on_missmatching_dates(LayoutNode $node, OpeningNode $opening, ClosingNode $closing)
    {
        $opening->getAttribute('date')->willReturn(new \DateTime('2010'));
        $closing->getAttribute('date')->willReturn(new \DateTime('2011'));
        $closing->getAttribute('nr_of_posts')->willReturn(0);
        $closing->getLineNr()->willReturn(1);
        $node->getChild('opening')->willReturn($opening);
        $node->getChild('closing')->willReturn($closing);
        $node->getChildren()->willReturn([$opening, $closing]);

        $this->afterLayoutNode($node);

        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_record_count(LayoutNode $node, OpeningNode $opening, ClosingNode $closing)
    {
        $date = new \DateTime;
        $opening->getAttribute('date')->willReturn($date);
        $closing->getAttribute('date')->willReturn($date);
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
