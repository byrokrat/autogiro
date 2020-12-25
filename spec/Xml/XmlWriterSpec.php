<?php

declare(strict_types=1);

namespace spec\byrokrat\autogiro\Xml;

use byrokrat\autogiro\Xml\XmlWriter;
use byrokrat\autogiro\Xml\XmlWritingVisitor;
use byrokrat\autogiro\Xml\Stringifier;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class XmlWriterSpec extends ObjectBehavior
{
    function let(Stringifier $stringifier)
    {
        $this->beConstructedWith($stringifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(XmlWriter::CLASS);
    }

    function it_can_generate_xml(Node $node)
    {
        $node->accept(Argument::type(XmlWritingVisitor::CLASS))->shouldBeCalled();
        $this->asXml($node)->shouldBeString();
    }
}
