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
 * Copyright 2016-20 Hannes Forsgård
 */

declare(strict_types=1);

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Exception\TreeException;

/**
 * Container for multiple visitors
 */
final class VisitorContainer extends Visitor
{
    use ErrorAwareTrait;

    /**
     * @var VisitorInterface[] Contained visitors
     */
    private $visitors = [];

    /**
     * Get contained visitors
     *
     * @return VisitorInterface[]
     */
    public function getVisitors(): array
    {
        return $this->visitors;
    }

    /**
     * Add a visitor to container
     */
    public function addVisitor(VisitorInterface $visitor): void
    {
        $this->visitors[] = $visitor;
    }

    /**
     * Delegate visit before to registered visitors
     */
    public function visitBefore(Node $node): void
    {
        parent::visitBefore($node);

        foreach ($this->visitors as $visitor) {
            $visitor->visitBefore($node);
        }
    }

    /**
     * Delegate visit after to registered visitors
     */
    public function visitAfter(Node $node): void
    {
        foreach ($this->visitors as $visitor) {
            $visitor->visitAfter($node);
        }

        parent::visitAfter($node);
    }

    /**
     * Reset the error container before a file node
     */
    public function beforeAutogiroFile(): void
    {
        $this->getErrorObject()->resetErrors();
    }

    /**
     * Throw exception if there are errors after iteration
     */
    public function afterAutogiroFile(): void
    {
        if ($this->getErrorObject()->hasErrors()) {
            throw new TreeException($this->getErrorObject()->getErrors());
        }
    }
}
