<?php
/**
 * This file is part of byrokrat\autogiro\Processor.
 *
 * byrokrat\autogiro\Processor is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * byrokrat\autogiro\Processor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with byrokrat\autogiro\Processor. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2016 Hannes Forsgård
 */

declare(strict_types = 1);

namespace byrokrat\autogiro\Processor;

use byrokrat\autogiro\Tree\TextNode;

/**
 * Validate the content of type nodes
 */
class TextProcessor extends Processor
{
    /**
     * Validate that text nodes contain values matching a regular expression
     */
    public function beforeTextNode(TextNode $textNode)
    {
        $regexp = $textNode->getAttribute('validation_regexp');

        if ($regexp && !preg_match($regexp, $textNode->getValue())) {
            $this->addError(
                "Text value '%s' does not match expected %s on line %s",
                $textNode->getValue(),
                $regexp,
                (string)$textNode->getLineNr()
            );
        }
    }
}
