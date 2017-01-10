<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\DateProcessor;
use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\Date\DateNode;
use PhpSpec\ObjectBehavior;

class DateProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateProcessor::CLASS);
    }

    function it_extends_processor()
    {
        $this->shouldHaveType(Processor::CLASS);
    }

    function it_fails_on_unvalid_date(DateNode $dateNode)
    {
        $dateNode->getLineNr()->willReturn(1);
        $dateNode->getType()->willReturn('DateNode');
        $dateNode->getValue()->willReturn('this-is-not-a-valid-date');

        $this->visitBefore($dateNode);

        $this->getErrors()->shouldHaveCount(1);
    }

    function it_creates_date(DateNode $dateNode)
    {
        $dateNode->getType()->willReturn('DateNode');
        $dateNode->getValue()->willReturn('20161109');

        $dateNode->setAttribute('date', new \DateTimeImmutable('20161109'))->shouldBeCalled();

        $this->visitBefore($dateNode);
        $this->getErrors()->shouldHaveCount(0);
    }
}
