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

use byrokrat\autogiro\Tree\Request\IncomingPaymentRequest;
use byrokrat\autogiro\Tree\Request\OutgoingPaymentRequest;
use byrokrat\autogiro\Tree\ImmediateDateNode;

/**
 * Validate intervals, repetitions and dates of payment requests
 */
class PaymentVisitor extends ErrorAwareVisitor
{
    public function beforeIncomingPaymentRequest(IncomingPaymentRequest $node): void
    {
        $this->validateImmediateDateWithInterval($node);
        $this->validateRepetitionsWithoutInterval($node);
    }

    public function beforeOutgoingPaymentRequest(OutgoingPaymentRequest $node): void
    {
        $this->validateImmediateDateWithInterval($node);
    }

    private function validateImmediateDateWithInterval(IncomingPaymentRequest $node): void
    {
        if ($node->getChild('date') instanceof ImmediateDateNode && $node->getChild('interval')->getValue() != '0') {
            $this->getErrorObject()->addError(
                "Immediate dates and intervals can not be mixed in payment on line %s",
                (string)$node->getLineNr()
            );
        }
    }

    private function validateRepetitionsWithoutInterval(IncomingPaymentRequest $node): void
    {
        if ($node->getChild('interval')->getValue() == '0' && trim($node->getChild('repetitions')->getValue()) != '') {
            $this->getErrorObject()->addError(
                "Repetitions set (%s) but no interval is definied on line %s",
                $node->getChild('repetitions')->getValue(),
                (string)$node->getLineNr()
            );
        }
    }
}
