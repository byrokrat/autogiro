<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Writer;

use byrokrat\autogiro\Writer\WriterFactory;
use byrokrat\autogiro\Writer\WriterInterface;
use byrokrat\banking\Bankgiro;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WriterFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WriterFactory::CLASS);
    }

    function it_creates_writers(Bankgiro $bankgiro, \DateTime $date)
    {
        $bankgiro->getNumber()->willReturn('');
        $date->format(Argument::any())->willReturn('');
        $this->createWriter('bgc_cust', $bankgiro, $date)->shouldHaveType(WriterInterface::CLASS);
    }
}
