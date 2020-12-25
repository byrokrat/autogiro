<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Xml;

use byrokrat\autogiro\Xml\XmlWritingVisitor;
use byrokrat\autogiro\Xml\Stringifier;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class XmlWritingVisitorSpec extends ObjectBehavior
{
    function let(\XMLWriter $writer, Stringifier $stringifier)
    {
        $this->beConstructedWith($writer, $stringifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(XmlWritingVisitor::CLASS);
    }

    function it_writes_elements_before(Node $node, $writer, $stringifier)
    {
        $node->getName()->willReturn('name');
        $writer->startElement('name')->shouldBeCalled();

        $node->getValue()->willReturn('raw_value');
        $stringifier->stringify('raw_value')->willReturn('stringified_value');
        $writer->text('stringified_value')->shouldBeCalled();

        $node->getType()->willReturn('type_name');
        $writer->writeAttribute('type', 'type_name')->shouldBeCalled();

        $this->visitBefore($node);
    }

    function it_ignores_type_if_same_as_name(Node $node, $writer)
    {
        $node->getName()->willReturn('foo');
        $writer->startElement('foo')->shouldBeCalled();

        $node->getType()->willReturn('foo');
        $writer->writeAttribute('type', 'foo')->shouldNotBeCalled();

        $node->getValue()->willReturn('');

        $this->visitBefore($node);
    }

    function it_ignores_void_values(Node $node, $writer)
    {
        $node->getName()->willReturn('name');
        $writer->startElement('name')->shouldBeCalled();

        $node->getType()->willReturn('type_name');
        $writer->writeAttribute('type', 'type_name')->shouldBeCalled();

        $node->getValue()->willReturn('');
        $writer->text(Argument::any())->shouldNotBeCalled();

        $this->visitBefore($node);
    }

    function it_closes_elements_after(Node $node, $writer)
    {
        $writer->endElement()->shouldBeCalled();
        $this->visitAfter($node);
    }
}
