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
use byrokrat\autogiro\Tree\PayeeBgcNumberNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\TextNode;

/**
 * Validate the content of text nodes
 */
class TextVisitor extends ErrorAwareVisitor
{
    public function beforeTextNode(TextNode $node)
    {
        $this->validateRegexp($node, "Text value '%s' does not match expected %s on line %s");
    }

    public function beforeRepetitionsNode(RepetitionsNode $node)
    {
        $this->validateRegexp($node, "Repeats '%s' does not match expected %s on line %s");
    }

    public function beforePayeeBgcNumberNode(PayeeBgcNumberNode $node)
    {
        $this->validateRegexp($node, "BGC customer number '%s' does not match expected %s on line %s");
    }

    public function beforePayerNumberNode(PayerNumberNode $node)
    {
        $this->validateRegexp($node, "Payer number '%s' does not match expected %s on line %s");
    }

    private function validateRegexp(TextNode $node, string $errorMsg)
    {
        if (!$node->hasAttribute('validation_regexp') || !$node->getAttribute('validation_regexp')) {
            return;
        }

        if (!preg_match($node->getAttribute('validation_regexp'), $node->getValue())) {
            $this->getErrorObject()->addError(
                $errorMsg,
                $node->getValue(),
                $node->getAttribute('validation_regexp'),
                (string)$node->getLineNr()
            );
        }
    }
}
