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
 * Copyright 2016-17 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\Node;

/**
 * Generic node visitor
 *
 * Will read the node type (eg. SomeNode) and dispatch a node specific method
 * if defined in visitor (eg. beforeSomeNode or afterSomeNode). By convention
 * such visitor methods should type hint the specific node type
 * (eg. beforeSomeNode(SomeNode $node)).
 */
class Visitor
{
    /**
     * Generic method for visiting a node before its children
     */
    public function visitBefore(Node $node)
    {
        $this->dispatch('before' . $node->getType(), $node);
    }

    /**
     * Generic method for visiting a node after its children
     */
    public function visitAfter(Node $node)
    {
        $this->dispatch('after' . $node->getType(), $node);
    }

    /**
     * Dispatch to method if method exists
     */
    private function dispatch(string $method, Node $node)
    {
        if (method_exists($this, $method)) {
            $this->$method($node);
        }
    }
}
