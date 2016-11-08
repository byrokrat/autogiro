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

namespace byrokrat\autogiro\Processor;

use byrokrat\autogiro\Tree\LayoutNode;

/**
 * Validate dates and record count in layout
 */
class LayoutProcessor extends Processor
{
    public function afterLayoutNode(LayoutNode $node)
    {
        if ($node->getChild('opening')->getAttribute('date') != $node->getChild('closing')->getAttribute('date')) {
            $this->addError(
                "Non-matching dates in opening and closing nodes (opening: %s, closing: %s) on line %s",
                $node->getChild('opening')->getAttribute('date')->format('Y-m-d'),
                $node->getChild('closing')->getAttribute('date')->format('Y-m-d'),
                (string)$node->getChild('closing')->getLineNr()
            );
        }

        if ($node->getChild('closing')->getAttribute('nr_of_posts') != count($node->getChildren())-2 ) {
            $this->addError(
                "Wrong record count in closing record (found: %s, expecting: %s) on line %s",
                (string)$node->getChild('closing')->getAttribute('nr_of_posts'),
                (string)(count($node->getChildren())-2),
                (string)$node->getChild('closing')->getLineNr()
            );
        }
    }
}
