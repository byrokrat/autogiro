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

namespace byrokrat\autogiro\Tree\Record\Request;

use byrokrat\autogiro\Tree\Record\RecordNode;
use byrokrat\autogiro\Tree\PayeeBankgiroNode;
use byrokrat\autogiro\Tree\PayerNumberNode;
use byrokrat\autogiro\Tree\TextNode;

/**
 * Node representing a request that a mandate received from the internet bank be rejected
 */
class RejectDigitalMandateRequestNode extends RecordNode
{
    public function __construct(
        int $lineNr,
        PayeeBankgiroNode $payeeBg,
        PayerNumberNode $payerNr,
        TextNode $space,
        TextNode $reject,
        array $void = []
    ) {
        $this->setChild('payee_bankgiro', $payeeBg);
        $this->setChild('payer_number', $payerNr);
        $this->setChild('space_1', $space);
        $this->setChild('reject', $reject);
        parent::__construct($lineNr, $void);
    }
}
