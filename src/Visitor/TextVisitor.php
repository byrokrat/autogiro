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

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\Interval;
use byrokrat\autogiro\Tree\PayeeBgcNumber;
use byrokrat\autogiro\Tree\PayerNumber;
use byrokrat\autogiro\Tree\Repetitions;
use byrokrat\autogiro\Tree\Text;

/**
 * Validate the content of text nodes
 */
class TextVisitor extends ErrorAwareVisitor
{
    public function beforeText(Text $node): void
    {
        $this->validateRegexp($node, "Text value '%s' does not match expected %s on line %s");
    }

    public function beforeRepetitions(Repetitions $node): void
    {
        $this->validateRegexp($node, "Repeats '%s' does not match expected %s on line %s");
    }

    public function beforePayeeBgcNumber(PayeeBgcNumber $node): void
    {
        $this->validateRegexp($node, "BGC customer number '%s' does not match expected %s on line %s");
    }

    public function beforePayerNumber(PayerNumber $node): void
    {
        $this->validateRegexp($node, "Payer number '%s' does not match expected %s on line %s");
    }

    private function validateRegexp(Text $node, string $errorMsg): void
    {
        if (!$node->getValidationRegexp()) {
            return;
        }

        if (!preg_match($node->getValidationRegexp(), $node->getValue())) {
            $this->getErrorObject()->addError(
                $errorMsg,
                $node->getValue(),
                $node->getValidationRegexp(),
                (string)$node->getLineNr()
            );
        }
    }
}
