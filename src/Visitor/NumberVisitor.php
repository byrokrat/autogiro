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

declare(strict_types = 1);

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Tree\Number;

/**
 * Validate the content of number nodes
 */
final class NumberVisitor extends Visitor
{
    use ErrorAwareTrait;

    public function beforeNumber(Number $node): void
    {
        if ($node->getValue() && !ctype_digit($node->getValue())) {
            $this->getErrorObject()->addError(
                "%s node value '%s' is not numerical on line %s",
                $node->getName(),
                (string)$node->getValue(),
                (string)$node->getLineNr()
            );
        }
    }
}
