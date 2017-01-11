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

namespace byrokrat\autogiro\Tree\Record\Request;

use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\DateNode;
use byrokrat\autogiro\Tree\IntervalNode;
use byrokrat\autogiro\Tree\RepetitionsNode;
use byrokrat\autogiro\Tree\TextNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\AmountNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;

/**
 * Node representing a request of an incoming transaction
 */
class IncomingTransactionRequestNode extends RecordNode
{
    public function __construct(
        int $lineNr,
        DateNode $date,
        IntervalNode $ival,
        RepetitionsNode $reps,
        TextNode $space,
        PayerNumberNode $payerNr,
        AmountNode $amount,
        PayeeBankgiroNode $payeeBg,
        TextNode $ref,
        array $void = []
    ) {
        $this->setChild('date', $date);
        $this->setChild('interval', $ival);
        $this->setChild('repetitions', $reps);
        $this->setChild('space_1', $space);
        $this->setChild('payer_number', $payerNr);
        $this->setChild('amount', $amount);
        $this->setChild('payee_bankgiro', $payeeBg);
        $this->setChild('reference', $ref);
        parent::__construct($lineNr, $void);
    }
}
