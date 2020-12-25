<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\SummaryVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Node;
use Money\Money;
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

    function it_validates_summary(
        Node $summary,
        Node $summaryAmount,
        Node $record,
        Node $recordAmount,
        $errorObj
    ) {
        $summary->getChild(Node::AMOUNT)->willReturn($summaryAmount);
        $summaryAmount->getValueFrom(Node::OBJ)->willReturn(Money::SEK('100'));
        $summary->getValueFrom('Text')->willReturn('foobar');

        $record->getName()->willReturn('foobar');
        $record->getChild(Node::AMOUNT)->willReturn($recordAmount);
        $recordAmount->getValueFrom(Node::OBJ)->willReturn(Money::SEK('100'));

        $this->afterRecord($record);
        $this->afterSummary($summary);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_uses_absolute_values_to_validate(
        Node $summary,
        Node $summaryAmount,
        Node $record,
        Node $recordAmount,
        $errorObj
    ) {
        $summary->getChild(Node::AMOUNT)->willReturn($summaryAmount);
        $summaryAmount->getValueFrom(Node::OBJ)->willReturn(Money::SEK('100'));
        $summary->getValueFrom('Text')->willReturn('foobar');

        $record->getName()->willReturn('foobar');
        $record->getChild(Node::AMOUNT)->willReturn($recordAmount);
        $recordAmount->getValueFrom(Node::OBJ)->willReturn(Money::SEK('-100'));

        $this->afterRecord($record);
        $this->afterSummary($summary);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_invalid_summary(
        Node $summary,
        Node $summaryAmount,
        Node $record,
        Node $recordAmount,
        $errorObj
    ) {
        $summary->getChild(Node::AMOUNT)->willReturn($summaryAmount);
        $summaryAmount->getValueFrom(Node::OBJ)->willReturn(Money::SEK('100'));
        $summary->getValueFrom('Text')->willReturn('foobar');
        $summary->getLineNr()->willReturn(1);

        $record->getName()->willReturn('foobar');
        $record->getChild(Node::AMOUNT)->willReturn($recordAmount);
        $recordAmount->getValueFrom(Node::OBJ)->willReturn(Money::SEK('200'));

        $this->afterRecord($record);
        $this->afterSummary($summary);

        $errorObj->addError(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_resets_on_file_node(
        Node $summary,
        Node $summaryAmount,
        Node $record,
        Node $recordAmount,
        $errorObj
    ) {
        $summary->getChild(Node::AMOUNT)->willReturn($summaryAmount);
        $summaryAmount->getValueFrom(Node::OBJ)->willReturn(Money::SEK('0'));
        $summary->getValueFrom('Text')->willReturn('foobar');
        $summary->getLineNr()->willReturn(1);

        $record->getName()->willReturn('foobar');
        $record->getChild(Node::AMOUNT)->willReturn($recordAmount);
        $recordAmount->getValueFrom(Node::OBJ)->willReturn(Money::SEK('100'));

        $this->afterRecord($record);
        $this->beforeAutogiroFile();
        $this->afterSummary($summary);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_ignores_summaries_with_no_amount(
        Node $summary,
        Node $summaryAmount,
        $errorObj
    ) {
        $summary->getChild(Node::AMOUNT)->willReturn($summaryAmount);
        $summaryAmount->getValueFrom(Node::OBJ)->willReturn(null);

        $this->afterSummary($summary);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
