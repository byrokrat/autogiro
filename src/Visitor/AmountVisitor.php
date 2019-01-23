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
 * Copyright 2016-19 Hannes Forsgård
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
final class AmountVisitor extends Visitor
{
    use ErrorAwareTrait;

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
            $invertSign = false;

            // due to charset issues unknown trailing signal chars are treated as 'å'
            if (!preg_match('/^[0-9åJKLMNOPQR]$/', mb_substr($signalStr, -1))) {
                $signalStr = mb_substr($signalStr, 0, -1) . '0';
                $invertSign = true;
            }

            $object = SEK::createFromSignalString($signalStr);

            if ($invertSign) {
                $object = $object->getInverted();
            }

            $node->addChild(new Obj($node->getLineNr(), $object));
        } catch (AmountException $e) {
            $this->getErrorObject()->addError(
                "Invalid signaled amount %s on line %s",
                (string)$node->getValueFrom('Text'),
                (string)$node->getLineNr()
            );
        }
    }
}
