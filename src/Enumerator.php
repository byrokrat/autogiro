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
 * Copyright 2016 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro;

use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Exception\LogicException;

/**
 * Tool to enumerate tree nodes of specified types
 */
class Enumerator
{
    /**
     * Magic method to register enumeration callbacks
     *
     * Usage: $enumerator->onNodeType($callback);
     *
     * @param string $name Name of node type to capure prefixed with on (eg onNodeType)
     * @param array  $args First argument must be a callable to be called with each capured node
     *
     * @throws LogicException If node type or callback is not specified
     */
    public function __call(string $name, array $args)
    {
        if (!preg_match('/^on[a-zA-Z0-9_]+$/', $name)) {
            throw new LogicException("Unknown method $name");
        }

        if (!is_callable($args[0])) {
            throw new LogicException('Enumeration callback must be callable');
        }

        $this->callbacks[substr($name, 2)] = $args[0];
    }

    /**
     * Enumerate nodes in tree
     */
    public function enumerate(Node $tree)
    {
        $this->dispatch($tree);

        foreach ($tree->getChildren() as $child) {
            $this->enumerate($child);
        }
    }

    /**
     * Invoke callback registered with node type
     */
    private function dispatch(Node $node)
    {
        if (isset($this->callbacks[$node->getType()])) {
            $this->callbacks[$node->getType()]($node);
        }
    }
}
