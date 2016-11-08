<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\FileNode;
use PhpSpec\ObjectBehavior;

class ProcessorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf(ConcreteProcessor::CLASS);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Processor::CLASS);
    }

    function it_defaults_to_no_error()
    {
        $this->hasErrors()->shouldEqual(false);
    }

    function it_can_store_errors()
    {
        $this->addError('foo %s %s', 'bar', 'baz');
        $this->hasErrors()->shouldEqual(true);
        $this->getErrors()->shouldEqual(['foo bar baz']);
    }

    function it_can_reset_errors(FileNode $fileNode)
    {
        $this->addError('foo %s', 'bar');
        $this->beforeFileNode($fileNode);
        $this->hasErrors()->shouldEqual(false);
    }
}

class ConcreteProcessor extends Processor
{
    public function addError(string $msg, string ...$args)
    {
        parent::addError($msg, ...$args);
    }
}
