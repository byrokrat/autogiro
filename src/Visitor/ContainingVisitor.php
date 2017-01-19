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
use byrokrat\autogiro\Exception\ContentException;

/**
 * Container for multiple visitors
 */
class ContainingVisitor extends ErrorAwareVisitor
{
    /**
     * @var Visitor[] Contained visitors
     */
    private $visitors;

    public function __construct(ErrorObject $errorObj, Visitor ...$visitors)
    {
        parent::__construct($errorObj);
        $this->visitors = $visitors;
    }

    /**
     * Get contained visitors
     *
     * @return Visitor[]
     */
    public function getVisitors(): array
    {
        return $this->visitors;
    }

    /**
     * Add a visitor to container
     */
    public function addVisitor(Visitor $visitor)
    {
        $this->visitors[] = $visitor;
    }

    /**
     * Delegate visit before to registered visitors
     */
    public function visitBefore(Node $node)
    {
        parent::visitBefore($node);

        foreach ($this->visitors as $visitor) {
            $visitor->visitBefore($node);
        }
    }

    /**
     * Delegate visit after to registered visitors
     */
    public function visitAfter(Node $node)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->visitAfter($node);
        }

        parent::visitAfter($node);
    }

    /**
     * Reset the error container before a file node
     */
    public function beforeFileNode()
    {
        $this->getErrorObject()->resetErrors();
    }

    /**
     * Throw exception if there are errors after iteration
     */
    public function afterFileNode()
    {
        if ($this->getErrorObject()->hasErrors()) {
            throw new ContentException($this->getErrorObject()->getErrors());
        }
    }
}
