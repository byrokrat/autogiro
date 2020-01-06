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

use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Obj;

/**
 * Visitor that expands date nodes
 *
 * Creates DateTime object as child 'Object'
 */
final class DateVisitor extends Visitor
{
    use ErrorAwareTrait;

    public function beforeDate(Node $node): void
    {
        if ($node->hasChild('Object')) {
            return;
        }

        $number = (string)$node->getValueFrom('Number');

        if (!trim($number)) {
            return;
        }

        $date = null;

        switch (strlen($number)) {
            case 6:
                $date = \DateTimeImmutable::createFromFormat('ymd', $number);
                break;
            case 8:
                $date = \DateTimeImmutable::createFromFormat('Ymd', $number);
                break;
            case 20:
                $date = \DateTimeImmutable::createFromFormat('YmdHis', substr($number, 0, -6));
                break;
        }

        if (!$date) {
            $this->getErrorObject()->addError(
                "Invalid date %s on line %s",
                $number,
                (string)$node->getLineNr()
            );
            return;
        }

        $node->addChild(new Obj($node->getLineNr(), $date));
    }
}
