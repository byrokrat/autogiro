<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Tree;

use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Visitor\VisitorInterface;
use PhpSpec\ObjectBehavior;

class NodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough(function () {
            return new class extends \byrokrat\autogiro\Tree\Node
            {
                public function getType(): string
                {
                }
            };
        });
    }

    function it_contains_a_line_number()
    {
        $this->beConstructedThrough(function () {
            return new class extends \byrokrat\autogiro\Tree\Node
            {
                function __construct()
                {
                    parent::__construct(5);
                }

                public function getType(): string
                {
                }
            };
        });

        $this->getLineNr()->shouldEqual(5);
    }

    function it_contains_a_value()
    {
        $this->beConstructedThrough(function () {
            return new class extends \byrokrat\autogiro\Tree\Node
            {
                function __construct()
                {
                    parent::__construct(1, 'foobar');
                }

                public function getType(): string
                {
                }
            };
        });

        $this->getValue()->shouldEqual('foobar');
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
        $this->getChild('this-is-not-definied')->shouldEqual(null);
    }

    function it_can_iterate_over_child_nodes(Node $node)
    {
        $this->addChild('node', $node);
        $this->getChildren()->shouldEqual([
            'node' => $node
        ]);
    }
}
