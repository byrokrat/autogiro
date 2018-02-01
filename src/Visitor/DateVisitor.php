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

use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\DateTimeNode;

/**
 * Visitor that expands date nodes
 *
 * Creates DateTime object as attribute 'date'
 */
class DateVisitor extends ErrorAwareVisitor
{
    public function beforeDateNode(DateNode $node): void
    {
        $this->setDateAttribute($node, $node->getValue());
    }

    public function beforeDateTimeNode(DateTimeNode $node): void
    {
        $this->setDateAttribute($node, substr($node->getValue(), 0, -6));
    }

    private function setDateAttribute(DateNode $node, string $value): void
    {
        if ($node->hasAttribute('date')) {
            return;
        }

        try {
            $node->setAttribute(
                'date',
                new \DateTimeImmutable($value)
            );
        } catch (\Exception $e) {
            $this->getErrorObject()->addError(
                "Invalid date %s on line %s",
                $node->getValue(),
                (string)$node->getLineNr()
            );
        }
    }
}
