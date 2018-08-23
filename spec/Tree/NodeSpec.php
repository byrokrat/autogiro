<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\NullNode;
use byrokrat\autogiro\Visitor\VisitorInterface;
use PhpSpec\ObjectBehavior;

class NodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Node::CLASS);
    }

    function it_contains_a_name()
    {
        $this->getName()->shouldEqual('Node');
    }

    function it_contains_a_type()
    {
        $this->getType()->shouldEqual('Node');
    }

    function it_can_set_its_name()
    {
        $this->setName('custom-name');
        $this->getName()->shouldReturn('custom-name');
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

    function it_is_not_a_null_node()
    {
        $this->isNull()->shouldReturn(false);
    }

    function it_accepts_a_visitor(VisitorInterface $visitor, Node $node)
    {
        $this->addChild('node', $node);

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

    function it_can_have_children(Node $node)
    {
        $this->hasChild('key')->shouldEqual(false);
        $this->addChild('key', $node);
        $this->hasChild('key')->shouldEqual(true);
        $this->getChild('key')->shouldEqual($node);
    }

    function it_defaults_undefined_child_to_null_node()
    {
        $this->getChild('this-is-not-definied')->shouldHaveType(NullNode::CLASS);
    }

    function it_identifies_child_nodes_insensitive_to_case(Node $node)
    {
        $this->addChild('lower', $node);
        $this->hasChild('LOWER')->shouldEqual(true);
        $this->getChild('LOWER')->shouldEqual($node);
    }

    function it_can_get_all_children(Node $node)
    {
        $this->addChild('node', $node);
        $this->getChildren()->shouldIterateAs([$node]);
    }

    function it_can_get_some_children(Node $a, Node $b, Node $c)
    {
        $this->addChild('foo', $a);
        $this->addChild('FOO', $b);
        $this->addChild('bar', $c);
        $this->getChildren('FOO')->shouldIterateAs([$a, $b]);
    }
}
