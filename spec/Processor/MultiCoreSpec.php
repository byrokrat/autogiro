<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Processor;

use byrokrat\autogiro\Processor\MultiCore;
use byrokrat\autogiro\Processor\Processor;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class MultiCoreSpec extends ObjectBehavior
{
    function let(Processor $processorA, Processor $processorB)
    {
        $this->beConstructedWith($processorA, $processorB);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MultiCore::CLASS);
    }

    function it_delegates_visit_before($processorA, $processorB, Node $node)
    {
        $this->visitBefore($node);
        $processorA->visitBefore($node)->shouldHaveBeenCalled();
        $processorB->visitBefore($node)->shouldHaveBeenCalled();
    }

    function it_delegates_visit_after($processorA, $processorB, Node $node)
    {
        $this->visitAfter($node);
        $processorA->visitAfter($node)->shouldHaveBeenCalled();
        $processorB->visitAfter($node)->shouldHaveBeenCalled();
    }

    function it_collects_errors($processorA, $processorB)
    {
        $processorA->getErrors()->willReturn(['A']);
        $processorB->getErrors()->willReturn(['B']);
        $this->getErrors()->shouldEqual(['A', 'B']);
        $this->hasErrors()->shouldEqual(true);
    }

    function it_can_register_new_processors(Processor $processor, Node $node)
    {
        $this->beConstructedWith();
        $this->addProcessor($processor);
        $this->visitBefore($node);
        $processor->visitBefore($node)->shouldHaveBeenCalled();
    }
}
