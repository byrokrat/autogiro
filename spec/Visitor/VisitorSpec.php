<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Tree\Node;
use PhpSpec\ObjectBehavior;

class VisitorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Visitor::CLASS);
    }

    function it_ignores_unknown_node_types(Node $node)
    {
        $node->getType()->willReturn('ThisIsNotAValidNode');
        $this->visitBefore($node)->shouldEqual(null);
        $this->visitAfter($node)->shouldEqual(null);
    }
}
