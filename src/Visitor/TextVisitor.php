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

use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\TextNode;

/**
 * Validate the content of text nodes
 */
class TextVisitor extends ErrorAwareVisitor
{
    /**
     * Validate that text nodes contain values matching a regular expression
     */
    public function beforeTextNode(TextNode $node)
    {
        $regexp = $node->getAttribute('validation_regexp');

        if ($regexp && !preg_match($regexp, $node->getValue())) {
            $this->getErrorObject()->addError(
                "Text value '%s' does not match expected %s on line %s",
                $node->getValue(),
                $regexp,
                (string)$node->getLineNr()
            );
        }
    }

    /**
     * Validate that repetition nodes contain values matching a regular expression
     */
    public function beforeRepetitionsNode(RepetitionsNode $node)
    {
        if (!preg_match($node->getAttribute('validation_regexp'), $node->getValue())) {
            $this->getErrorObject()->addError(
                "Repeats '%s' does not match expected %s on line %s",
                $node->getValue(),
                $node->getAttribute('validation_regexp'),
                (string)$node->getLineNr()
            );
        }
    }
}
