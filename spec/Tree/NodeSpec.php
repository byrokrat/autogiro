<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Visitor\Visitor;
use byrokrat\autogiro\Exception\LogicException;
use PhpSpec\ObjectBehavior;

class NodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Node');
    }

    function it_contains_a_line_number()
    {
        $this->beConstructedWith(5);
        $this->getLineNr()->shouldEqual(5);
    }

    function it_contains_a_value()
    {
        $this->beConstructedWith(1, 'foobar');
        $this->getValue()->shouldEqual('foobar');
    }

    function it_accepts_a_visitor(Visitor $visitor, Node $node)
    {
        $this->setChild('node', $node);

        $this->accept($visitor);

        $visitor->visitBefore($this)->shouldHaveBeenCalled();
        $node->accept($visitor)->shouldHaveBeenCalled();
        $visitor->visitAfter($this)->shouldHaveBeenCalled();
    }

    function it_can_save_attributes()
    {
        $this->hasAttribute('key')->shouldEqual(false);
        $this->setAttribute('key', 'value');
        $this->hasAttribute('key')->shouldEqual(true);
        $this->getAttribute('key')->shouldEqual('value');
    }

    function it_defualts_attributes_to_null()
    {
        $this->getAttribute('does-not-exist')->shouldEqual(null);
    }

    function it_can_iterate_over_attributes()
    {
        $this->setAttribute('foo', 'bar');
        $this->setAttribute('bar', 'foo');
        $this->getAttributes()->shouldEqual([
            'foo' => 'bar',
            'bar' => 'foo'
        ]);
    }

    function it_can_save_children(Node $node)
    {
        $this->hasChild('key')->shouldEqual(false);
        $this->setChild('key', $node);
        $this->hasChild('key')->shouldEqual(true);
        $this->getChild('key')->shouldEqual($node);
    }

    function it_throws_exception_on_unknown_child()
    {
        $this->shouldThrow(LogicException::CLASS)->duringGetChild('does-not-exist');
    }

    function it_can_iterate_over_child_nodes(Node $node)
    {
        $this->setChild('node', $node);
        $this->getChildren()->shouldEqual([
            'node' => $node
        ]);
    }
}
