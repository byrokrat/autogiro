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

use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\Obj;
use byrokrat\amount\Currency\SEK;
use byrokrat\amount\Exception as AmountException;

/**
 * Create amount object under child 'Object'
 */
class AmountVisitor extends ErrorAwareVisitor
{
    public function beforeAmount(Node $node): void
    {
        if ($node->hasChild('Object')) {
            return;
        }

        $signalStr = (string)$node->getValueFrom('Text');

        if (trim($signalStr) == '') {
            return;
        }

        try {
            $node->addChild(new Obj($node->getLineNr(), SEK::createFromSignalString($signalStr)));
        } catch (AmountException $e) {
            $this->getErrorObject()->addError(
                "Invalid signaled amount %s on line %s",
                $signalStr,
                (string)$node->getLineNr()
            );
        }
    }
}
