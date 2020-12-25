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

declare(strict_types=1);

namespace byrokrat\autogiro\Visitor;

use byrokrat\autogiro\Intervals;
use byrokrat\autogiro\Tree\Node;
use byrokrat\autogiro\Tree\ImmediateDate;

/**
 * Validate intervals, repetitions and dates of payment requests
 */
final class PaymentVisitor extends Visitor
{
    use ErrorAwareTrait;

    public function beforeIncomingPaymentRequest(Node $node): void
    {
        $this->validateImmediateDateWithInterval($node);
        $this->validateRepetitionsWithoutInterval($node);
    }

    public function beforeOutgoingPaymentRequest(Node $node): void
    {
        $this->validateImmediateDateWithInterval($node);
        $this->validateRepetitionsWithoutInterval($node);
    }

    private function validateImmediateDateWithInterval(Node $node): void
    {
        if (
            $node->getChild('date') instanceof ImmediateDate
            && $node->getChild('interval')->getValueFrom(Node::NUMBER) != Intervals::INTERVAL_ONCE
        ) {
            $this->getErrorObject()->addError(
                "Immediate dates and intervals can not be mixed in payment on line %s",
                (string)$node->getLineNr()
            );
        }
    }

    private function validateRepetitionsWithoutInterval(Node $node): void
    {
        if (
            $node->getChild('interval')->getValueFrom(Node::NUMBER) == Intervals::INTERVAL_ONCE
            && trim($node->getValueFrom('repetitions')) != ''
        ) {
            $this->getErrorObject()->addError(
                "Repetitions set (%s) but interval is once (%s) on line %s",
                $node->getValueFrom('repetitions'),
                $node->getChild('interval')->getValueFrom(Node::NUMBER),
                (string)$node->getLineNr()
            );
        }
    }
}
