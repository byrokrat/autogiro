<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\CountingVisitor;
use byrokrat\autogiro\Visitor\ErrorAwareVisitor;
use byrokrat\autogiro\Visitor\ErrorObject;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CountingVisitorSpec extends ObjectBehavior
{
    function let(ErrorObject $errorObj)
    {
        $this->beConstructedWith($errorObj);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CountingVisitor::CLASS);
    }

    function it_is_an_error_aware_visitor()
    {
        $this->shouldHaveType(ErrorAwareVisitor::CLASS);
    }

    function it_validates_record_counts(Node $count, Node $number, Node $text, Node $record, $errorObj)
    {
        $count->getChild('Number')->willReturn($number);
        $count->getChild('Text')->willReturn($text);
        $number->getValue()->willReturn('0000001');
        $text->getValue()->willReturn('foobar');

        $record->getName()->willReturn('foobar');

        $this->beforeRecord($record);
        $this->beforeCount($count);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_validates_section_counts(Node $count, Node $number, Node $text, Node $section, $errorObj)
    {
        $count->getChild('Number')->willReturn($number);
        $count->getChild('Text')->willReturn($text);
        $number->getValue()->willReturn('0000001');
        $text->getValue()->willReturn('foobar');

        $section->getName()->willReturn('foobar');

        $this->beforeSection($section);
        $this->beforeCount($count);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    function it_fails_on_invalid_count(Node $count, Node $number, Node $text, Node $record, $errorObj)
    {
        $count->getLineNr()->willReturn(1);
        $count->getChild('Number')->willReturn($number);
        $count->getChild('Text')->willReturn($text);
        $number->getValue()->willReturn('0000000');
        $text->getValue()->willReturn('foobar');

        $record->getName()->willReturn('foobar');

        $this->beforeRecord($record);
        $this->beforeCount($count);

        $errorObj->addError(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    function it_resets_count_on_file_node(Node $count, Node $number, Node $text, Node $record, $errorObj)
    {
        $count->getChild('Number')->willReturn($number);
        $count->getChild('Text')->willReturn($text);
        $number->getValue()->willReturn('0000000');
        $text->getValue()->willReturn('foobar');

        $record->getName()->willReturn('foobar');

        $this->beforeRecord($record);
        $this->beforeAutogiroFile();
        $this->beforeCount($count);

        $errorObj->addError(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
