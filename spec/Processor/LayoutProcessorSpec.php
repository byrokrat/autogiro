<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\LayoutProcessor;
use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\LayoutNode;
use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\Record\OpeningRecordNode;
use byrokrat\autogiro\Tree\Record\ClosingRecordNode;
use byrokrat\autogiro\Tree\Date\DateNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;

class LayoutProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LayoutProcessor::CLASS);
    }

    function it_extends_processor()
    {
        $this->shouldHaveType(Processor::CLASS);
    }

    function it_fails_on_missmatching_dates(
        OpeningRecordNode $opening,
        ClosingRecordNode $closing,
        DateNode $dateA,
        DateNode $dateB
    ) {
        $dateA->getValue()->willReturn('2010');
        $opening->getChild('date')->willReturn($dateA);

        $this->beforeOpeningRecordNode($opening);

        $dateB->getValue()->willReturn('2011');
        $closing->getChild('date')->willReturn($dateB);
        $closing->getLineNr()->willReturn(1);

        $this->beforeClosingRecordNode($closing);

        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldHaveCount(1);
    }

    function it_fails_on_wrong_record_count(
        LayoutNode $layout,
        OpeningRecordNode $opening,
        RecordNode $record,
        ClosingRecordNode $closing,
        TextNode $nrOfPosts
    ) {

        $nrOfPosts->getValue()->willReturn('2');
        $closing->getChild('nr_of_posts')->willReturn($nrOfPosts);

        $this->afterClosingRecordNode($closing);

        $layout->getChildren()->willReturn([$opening, $record, $closing]);
        $layout->getLineNr()->willReturn(1);

        $this->afterLayoutNode($layout);

        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldHaveCount(1);
    }
}
