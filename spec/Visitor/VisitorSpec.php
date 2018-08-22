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

    function it_ignores_unknown_nodes(Node $node)
    {
        $node->getName()->willReturn('ThisIsNotAValidNode');
        $node->getType()->willReturn('AndNeitherIsThis');
        $this->visitBefore($node)->shouldEqual(null);
        $this->visitAfter($node)->shouldEqual(null);
    }

    function it_dispatches_before_name_hook_as_method(Node $node)
    {
        $this->beConstructedThrough(function () {
            return new class extends \byrokrat\autogiro\Visitor\Visitor {
                public $called = false;

                function beforeNode($node)
                {
                    $this->called = true;
                }

                function isCalledAsBeforeNode(): bool
                {
                    return $this->called;
                }
            };
        });
        $node->getName()->willReturn('Node');
        $node->getType()->willReturn('');
        $this->visitBefore($node);
        $this->shouldBeCalledAsBeforeNode();
    }

    function it_dispatches_before_name_hook_as_property(Node $node)
    {
        $this->beConstructedThrough(function () {
            $visitor = new class extends \byrokrat\autogiro\Visitor\Visitor {
                public $called = false;

                function isCalledAsBeforeNode(): bool
                {
                    return $this->called;
                }
            };

            $visitor->beforeNode = function ($node) use ($visitor) {
                $visitor->called = true;
            };

            return $visitor;
        });
        $node->getName()->willReturn('Node');
        $node->getType()->willReturn('');
        $this->visitBefore($node);
        $this->shouldBeCalledAsBeforeNode();
    }

    function it_dispatches_after_name_hook_as_method(Node $node)
    {
        $this->beConstructedThrough(function () {
            return new class extends \byrokrat\autogiro\Visitor\Visitor {
                public $called = false;

                function afterNode($node)
                {
                    $this->called = true;
                }

                function isCalledAsAfterNode(): bool
                {
                    return $this->called;
                }
            };
        });
        $node->getName()->willReturn('Node');
        $node->getType()->willReturn('');
        $this->visitAfter($node);
        $this->shouldBeCalledAsAfterNode();
    }

    function it_dispatches_after_name_hook_as_property(Node $node)
    {
        $this->beConstructedThrough(function () {
            $visitor = new class extends \byrokrat\autogiro\Visitor\Visitor {
                public $called = false;

                function isCalledAsAfterNode(): bool
                {
                    return $this->called;
                }
            };

            $visitor->afterNode = function ($node) use ($visitor) {
                $visitor->called = true;
            };

            return $visitor;
        });
        $node->getName()->willReturn('Node');
        $node->getType()->willReturn('');
        $this->visitAfter($node);
        $this->shouldBeCalledAsAfterNode();
    }

    function it_dispatches_before_type_hook_as_method(Node $node)
    {
        $this->beConstructedThrough(function () {
            return new class extends \byrokrat\autogiro\Visitor\Visitor {
                public $called = false;

                function beforeNode($node)
                {
                    $this->called = true;
                }

                function isCalledAsBeforeNode(): bool
                {
                    return $this->called;
                }
            };
        });
        $node->getName()->willReturn('');
        $node->getType()->willReturn('Node');
        $this->visitBefore($node);
        $this->shouldBeCalledAsBeforeNode();
    }

    function it_dispatches_before_type_hook_as_property(Node $node)
    {
        $this->beConstructedThrough(function () {
            $visitor = new class extends \byrokrat\autogiro\Visitor\Visitor {
                public $called = false;

                function isCalledAsBeforeNode(): bool
                {
                    return $this->called;
                }
            };

            $visitor->beforeNode = function ($node) use ($visitor) {
                $visitor->called = true;
            };

            return $visitor;
        });
        $node->getName()->willReturn('');
        $node->getType()->willReturn('Node');
        $this->visitBefore($node);
        $this->shouldBeCalledAsBeforeNode();
    }

    function it_dispatches_after_type_hook_as_method(Node $node)
    {
        $this->beConstructedThrough(function () {
            return new class extends \byrokrat\autogiro\Visitor\Visitor {
                public $called = false;

                function afterNode($node)
                {
                    $this->called = true;
                }

                function isCalledAsAfterNode(): bool
                {
                    return $this->called;
                }
            };
        });
        $node->getName()->willReturn('');
        $node->getType()->willReturn('Node');
        $this->visitAfter($node);
        $this->shouldBeCalledAsAfterNode();
    }

    function it_dispatches_after_type_hook_as_property(Node $node)
    {
        $this->beConstructedThrough(function () {
            $visitor = new class extends \byrokrat\autogiro\Visitor\Visitor {
                public $called = false;

                function isCalledAsAfterNode(): bool
                {
                    return $this->called;
                }
            };

            $visitor->afterNode = function ($node) use ($visitor) {
                $visitor->called = true;
            };

            return $visitor;
        });
        $node->getName()->willReturn('');
        $node->getType()->willReturn('Node');
        $this->visitAfter($node);
        $this->shouldBeCalledAsAfterNode();
    }
}
