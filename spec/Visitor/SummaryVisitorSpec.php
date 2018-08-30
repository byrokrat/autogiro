<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\SummaryVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Node;
use byrokrat\amount\Currency\SEK;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SummaryVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SummaryVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_validates_summary(
        Node $summary,
        Node $summaryAmountNode,
        Node $sumaryObjectNode,
        Node $text,
        Node $record,
        Node $recordAmountNode,
        Node $recordObjectNode,
        $errorObj
    ) {
        $summary->getChild('Amount')->willReturn($summaryAmountNode);
        $summaryAmountNode->getChild('Object')->willReturn($sumaryObjectNode);
        $sumaryObjectNode->getValue()->willReturn(new SEK('100'));
        $summary->getChild('Text')->willReturn($text);
        $text->getValue()->willReturn('foobar');

        $record->getName()->willReturn('foobar');
        $record->getChild('Amount')->willReturn($recordAmountNode);
        $recordAmountNode->getChild('Object')->willReturn($recordObjectNode);
        $recordObjectNode->getValue()->willReturn(new SEK('100'));

        $this->afterRecord($record);
        $this->afterSummary($summary);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_uses_absolute_values_to_validate(
        Node $summary,
        Node $summaryAmountNode,
        Node $sumaryObjectNode,
        Node $text,
        Node $record,
        Node $recordAmountNode,
        Node $recordObjectNode,
        $errorObj
    ) {
        $summary->getChild('Amount')->willReturn($summaryAmountNode);
        $summaryAmountNode->getChild('Object')->willReturn($sumaryObjectNode);
        $sumaryObjectNode->getValue()->willReturn(new SEK('100'));
        $summary->getChild('Text')->willReturn($text);
        $text->getValue()->willReturn('foobar');

        $record->getName()->willReturn('foobar');
        $record->getChild('Amount')->willReturn($recordAmountNode);
        $recordAmountNode->getChild('Object')->willReturn($recordObjectNode);
        $recordObjectNode->getValue()->willReturn(new SEK('-100'));

        $this->afterRecord($record);
        $this->afterSummary($summary);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_invalid_summary(
        Node $summary,
        Node $summaryAmountNode,
        Node $sumaryObjectNode,
        Node $text,
        Node $record,
        Node $recordAmountNode,
        Node $recordObjectNode,
        $errorObj
    ) {
        $summary->getChild('Amount')->willReturn($summaryAmountNode);
        $summaryAmountNode->getChild('Object')->willReturn($sumaryObjectNode);
        $sumaryObjectNode->getValue()->willReturn(new SEK('100'));
        $summary->getChild('Text')->willReturn($text);
        $text->getValue()->willReturn('foobar');
        $summary->getLineNr()->willReturn(1);

        $record->getName()->willReturn('foobar');
        $record->getChild('Amount')->willReturn($recordAmountNode);
        $recordAmountNode->getChild('Object')->willReturn($recordObjectNode);
        $recordObjectNode->getValue()->willReturn(new SEK('200'));

        $this->afterRecord($record);
        $this->afterSummary($summary);

        $errorObj->addError(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_resets_on_file_node(
        Node $summary,
        Node $summaryAmountNode,
        Node $sumaryObjectNode,
        Node $text,
        Node $record,
        Node $recordAmountNode,
        Node $recordObjectNode,
        $errorObj
    ) {
        $summary->getChild('Amount')->willReturn($summaryAmountNode);
        $summaryAmountNode->getChild('Object')->willReturn($sumaryObjectNode);
        $sumaryObjectNode->getValue()->willReturn(new SEK('0'));
        $summary->getChild('Text')->willReturn($text);
        $text->getValue()->willReturn('foobar');
        $summary->getLineNr()->willReturn(1);

        $record->getName()->willReturn('foobar');
        $record->getChild('Amount')->willReturn($recordAmountNode);
        $recordAmountNode->getChild('Object')->willReturn($recordObjectNode);
        $recordObjectNode->getValue()->willReturn(new SEK('100'));

        $this->afterRecord($record);
        $this->beforeAutogiroFile();
        $this->afterSummary($summary);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_ignores_summaries_with_no_amount(
        Node $summary,
        Node $summaryAmountNode,
        Node $sumaryObjectNode,
        $errorObj
    ) {
        $summary->getChild('Amount')->willReturn($summaryAmountNode);
        $summaryAmountNode->getChild('Object')->willReturn($sumaryObjectNode);
        $sumaryObjectNode->getValue()->willReturn(null);

        $this->afterSummary($summary);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
