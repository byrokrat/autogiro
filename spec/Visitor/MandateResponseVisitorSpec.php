<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\MandateResponseVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\FileNode;
use byrokrat\autogiro\Tree\RecordNode;
use byrokrat\autogiro\Tree\Response\ResponseOpening;
use byrokrat\autogiro\Tree\Response\MandateResponseClosing;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\TextNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MandateResponseVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MandateResponseVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_fails_on_missmatching_dates(
        ResponseOpening $opening,
        MandateResponseClosing $closing,
        DateNode $dateA,
        DateNode $dateB,
        $errorObj
    ) {
        $dateA->getValue()->willReturn('2010');
        $opening->getChild('date')->willReturn($dateA);

        $this->beforeResponseOpening($opening);

        $dateB->getValue()->willReturn('2011');
        $closing->getChild('date')->willReturn($dateB);
        $closing->getLineNr()->willReturn(1);

        $this->beforeMandateResponseClosing($closing);

        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_fails_on_wrong_record_count(
        FileNode $fileNode,
        ResponseOpening $opening,
        RecordNode $record,
        MandateResponseClosing $closing,
        TextNode $nrOfPosts,
        $errorObj
    ) {
        $nrOfPosts->getValue()->willReturn('2');
        $closing->getChild('nr_of_posts')->willReturn($nrOfPosts);

        $this->afterMandateResponseClosing($closing);

        $fileNode->getChildren()->willReturn([$opening, $record, $closing]);
        $fileNode->getLineNr()->willReturn(1);

        $this->afterFileNode($fileNode);

        $errorObj->addError(Argument::type('string'), Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
