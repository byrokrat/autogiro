<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Xml;

use byrokrat\autogiro\Xml\XmlWriterFactory;
use byrokrat\autogiro\Xml\XmlWriter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class XmlWriterFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(XmlWriterFactory::CLASS);
    }

    function it_can_create_writers()
    {
        $this->createXmlWriter()->shouldHaveType(XmlWriter::CLASS);
    }
}
