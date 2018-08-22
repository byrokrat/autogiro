<?php
/**
 * This file is part of byrokrat\autogiro.
 *
 * byrokrat\autogiro is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat\autogiro is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat\autogiro. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016-18 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Tree;

use byrokrat\autogiro\Visitor\VisitorInterface;

/**
 * Defines a node in the parse tree
 */
abstract class Node
{
    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var Node[]
     */
    private $children = [];

    /**
     * @var int
     */
    private $lineNr = 0;

    /**
     * @var mixed
     */
    private $value = '';

    public function __construct(int $lineNr = 0, $value = '')
    {
        $this->lineNr = $lineNr;
        $this->value = $value;
    }

    /**
     * Accept a visitor
     */
    public function accept(VisitorInterface $visitor): void
    {
        $visitor->visitBefore($this);

        foreach ($this->getChildren() as $node) {
            $node->accept($visitor);
        }

        $visitor->visitAfter($this);
    }

    /**
     * Get line number this node definition started at
     */
    public function getLineNr(): int
    {
        return $this->lineNr;
    }

    /**
     * Get node name
     */
    public function getName(): string
    {
        return basename(str_replace('\\', '/', get_class($this)));
    }

    /**
     * Get node type identifier
     */
    abstract public function getType(): string;

    /**
     * Get raw value wrapped by node
     *
     * @return mixed Loaded node value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set a custom attribute on node
     *
     * @param string $name  Name of attribute
     * @param mixed  $value Value of attribute
     */
    public function setAttribute(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Get custom attribute
     *
     * @param  string $name Name of attribute
     * @return mixed  Value of attribute
     */
    public function getAttribute(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Check if attribute has been set
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Get all registered attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set a child node
     */
    public function addChild(string $name, Node $child): void
    {
        $this->children[$name] = $child;
    }

    /**
     * Get child node
     */
    public function getChild(string $name): ?Node
    {
        if (!$this->hasChild($name)) {
            return null;
        }

        return $this->children[$name];
    }

    /**
     * Check if child exists
     */
    public function hasChild(string $name): bool
    {
        return isset($this->children[$name]);
    }

    /**
     * Get registered child nodes
     *
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
