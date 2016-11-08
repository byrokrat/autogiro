<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro;

use byrokrat\autogiro\Enumerator;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Exception\LogicException;
use PhpSpec\ObjectBehavior;

class EnumeratorSpec extends ObjectBehavior
{
    function let(Node $parent, Node $childA, Node $childB, Node $grandchild)
    {
        $grandchild->getType()->willReturn('Grandchild');
        $grandchild->getChildren()->willReturn([]);

        $childA->getChildren()->willReturn([$grandchild]);
        $childA->getType()->willReturn('Child');

        $childB->getChildren()->willReturn([]);
        $childB->getType()->willReturn('Child');

        $parent->getChildren()->willReturn([$childA, $childB]);
        $parent->getType()->willReturn('Parent');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Enumerator::CLASS);
    }

    function it_fails_on_unknown_method()
    {
        $this->shouldThrow(LogicException::CLASS)->during('method_not_defined');
    }

    function it_fails_on_non_callable()
    {
        $this->shouldThrow(LogicException::CLASS)->duringOnNode('this-is-not-a-callable');
    }

    function it_enumerates_the_base_node($parent, Callback $callback)
    {
        $this->onParent($callback);
        $this->enumerate($parent);
        $callback->__invoke($parent)->shouldHaveBeenCalled();
    }

    function it_enumerates_children($parent, $childA, $childB, $grandchild, Callback $callback)
    {
        $this->onChild($callback);
        $this->enumerate($parent);
        $callback->__invoke($childA)->shouldHaveBeenCalled();
        $callback->__invoke($childB)->shouldHaveBeenCalled();
        $callback->__invoke($parent)->shouldNotHaveBeenCalled();
        $callback->__invoke($grandchild)->shouldNotHaveBeenCalled();
    }
}

class Callback
{
    public function __invoke()
    {
    }
}
